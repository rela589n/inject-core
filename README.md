Inject Core is simple abstraction layer, which allows you to separate your application code from framework-specific
dependency injection containers. This package doesn't allow direct container usage in arbitrary places, but provides
elegant way to do dependency injection for compile-time arguments at runtime during objects creation.

Foundation
----------

PHP frameworks (Symfony and Laravel) as of now either at all prohibit service container usage in the client code, or allow its
uncontrolled usage.

### Service Container in Laravel

It provides global `app()`, `make()`, `resolve()`, `app()->make()`, `app()->makeWith()`, `Container::getInstance()` access
points to container, and it's up to you how you will use it. In many cases such permissiveness leads to unsupported,
untestable code, which is hard to maintain, and really difficult to rewrite. You can't just cover it with tests and refactor,
because there are lots of places in class where global container is used. This happens because if one can do something this
way - someone will definitely do it this way. From my point of view, it is better to bring some restrictions and standards
about how use container and how to not use it, while keeping some flexibility necessary for convenient development.

### DI in Symfony

Symfony, from other hand, restricts us from direct container usage as much as possible. I like this approach, because we can't
just make a mess from our code. If all dependencies are provided with constructor, we can cover our code with tests and
therefore, easily refactor it.

However, in order to use the service container, all the dependencies for class must be known at compile-time. If we need some
runtime dependencies for class, container just can't be used for it. We would have no other option than to create additional
"factory" class to utilize service container for compile-time dependencies and accept run-time dependencies in the `create()`
method. In other words, this factory is defined as a `service`, and all it's dependencies are compile-time (known beforehand).

This way, it is no-longer easy to create objects, because:

1. Second class is required (factory for creation) apart from the class itself;
2. This is necessary to bring factory everywhere we want to create object. It implies that if in class `C` we would like
   to create an object `O`, which has factory `F`, then `C` would need to have new field for `F`, new constructor argument
   with `F`, and new service container mapping about `F` argument for `C`. Also, the factory itself requires service container
   definition;
3. Code becomes less readable, because you see factories, not real objects which are created;
4. It is not possible to quickly refactor such code. For example, in order to move method `C::m()` which instantiates
   object `O` using factory `F` into separate class, we have to move its factory `C::$f` along the way (and not forget
   about `services.yml` modification for class which method was moved in).

Do you see the point? One modification requires another modification when we do refactor. It shouldn't be like this. It
wouldn't happen if code followed information expert GRASP principle. But wait... How does separation of object creation
responsibility from the object itself violates encapsulation? Actually, introduction of factory may or may not violate
encapsulation. It depends on what is considered as object internals, and what is not. But in many cases, factories are created
to provide object with its internal compile-time dependencies. Yet, client code would not like to care about how object
operates internally and what internal dependencies it uses. If you use `new JsonResponse($jsonArray)`, you don't care if it
internally uses `JsonEncoder` class or `json_encode` function or anything else.

Let's consider an example of encapsulation may be violated. Imagine 2D drawing application and `Point(:x, :y)` Value Object.
It holds coordinates as private properties and encapsulates calculation logic like distance from this to other
point `distance(Point $other): float` and some other useful methods. Now, just think we separated it into parts such that `X`
and `Y` doesn't belong to single point object anymore. They are just `X` and just `Y`. For the client code it will be
necessary to always have `X` together with `Y`. Otherwise, we will not be able to implement business logic. In any place we
see `X`, `Y` will be definitely there. Such bundles of related pieces should reside within the same object.

The same goes for factories. Any time we want to create object, we bring its factory along. We move it somewhere else - we
move factory also. You may think that factories have their purpose to create the object, and it is logical if we want to
create object somewhere else, then factory should be also there. But it is only partially true. If objects had no compile-time
dependencies, we would always use `new SomeObject()` syntax. We can refactor it in any way we want, extract method to another
class, and there's no need to move any factories along.

To summarize, object instantiation is the responsibility of the object itself. Programming languages were designed such that
constructors are used for this purpose. Object instantiation code inherently belongs to the class of object.

Understanding the problem
-------------------------

From the above explanations a new question arises: is it possible to prevent factories overhead, while still using DI for
all the dependencies, and not duplicate compile-time parameters in all places where object is instantiated?

At first glance, it doesn't seem to be feasible. If we remove factory, and pass everything directly into constructor, then
things become a lot worse. All object compile-time dependencies are brought to all classes, where this object is created.
Instead of single factory, we bring all properties, which factory held before.

I hope you see the big difference between compile-time and run-time dependencies. Though, constructor is used for both,
and it can't be changed from programming language perspective. Still, we can try to implement it in PHP code.

Here's an example of class evolution over the time:

```php
class GeneratedKeyPair
{
    public function __construct(private int $userId) {}

    public function publicKey(): string
    {
        return uniqid((string)$this->userId);
    }

    public function privateKey(): string
    {
        return uniqid((string)$this->userId, true);
    }
}
```

It has single run-time dependency `$userId`. For client, this class is really easy to use:

```php
$pair = new GeneratedKeyPair($userId);
$this->keyStorage->saveKeys($pair);
// other code
```

Now, problem arises. Someone found that `uniqid` is bad choice for keys generation, and we should use another full-fledged
library specifically designed for it. Main class of library is `KeyGenerator`, and it requires some env parameters to be
passed in, so we can't just create instance of `KeyGenerator` inside of `GeneratedKeyPair`. If we add `KeyGenerator` as
constructor argument, this will lead to modification of all the places where `GeneratedKeyPair` was created.

```php
$pair = new GeneratedKeyPair($this->keyGenerator, $userId);
$this->keyStorage->saveKeys($pair);
```

But why does client code cares about some `KeyGenerator`? Before modification, an `uniqid` worked internally, and it was
easy for us to use this class. Now, `$this->keyGenerator` part of guts is exposed, and it follows us anywhere whe want to
create the object. If this internal `$keyGenerator` will be prone for change once again (yes, one more time another library),
all instantiation places will come across the same modification. Yes, we can introduce factory at this step, but it doesn't
solve the root cause of problem, and it won't hide implementation details from client code. Factory is just pretence of
solution, still it is not natural.

Look at this crap and compare it with initial `new GeneratedKeyPair($userId);`

```php
$pair = $this->generatedKeyPairFactory->createKeyPair($userId);
$this->keyStorage->saveKeys($pair);
```

I am really glad that class is called `GeneratedKeyPair`, not a `KeyPairFactory`, because otherwise outer factory would be
factory of factory. Look at this crap:

```php
class C
{
    private KeyStorage $keyStorage;
    private KeyPairFactoryFactory $keyPairFactoryFactory;

    public function __construct(
        KeyStorage $keyStorage,
        KeyPairFactoryFactory $keyPairFactoryFactory
    ) {
        $this->keyStorage = $keyStorage;
        $this->keyPairFactoryFactory = $keyPairFactoryFactory;        
    }
    
    public function doStuff(int $userId)
    {
        $keysFactory = $this->keyPairFactoryFactory->createKeyPairFactory($userId);
        $this->keyStorage->saveKeys($keysFactory);
        //...
    }
}
```

And compare it with this neat code:

```php
$pair = new GeneratedKeyPair($userId);
$this->keyStorage->saveKeys($pair);
```

NO factories, NO overhead! Readable code, ease of support, profit!

Solution
-------

The solution is to separate compile-time and run-time dependencies in the first place.
Enough explanations already. Here's final code:

Client code doesn't know anything about internals:

```php
$pair = new GeneratedKeyPair($userId);
$this->keyStorage->saveKeys($pair);
```

The object itself is responsible for all its internals as before:

```php
class GeneratedKeyPair
{
    private KeyGenerator $keyGenerator;

    public function __inject(KeyGenerator $keyGenerator): void
    {
        $this->keyGenerator = $keyGenerator;     
    }

    public function __construct(private int $userId) 
    {
        inject($this);
    }

    public function publicKey(): string
    {
        return $this->keyGenerator->generatePublicKey($this->userId);
    }

    public function privateKey(): string
    {
        return $this->keyGenerator->generatePrivateKey($this->userId);
    }
}
```
