When you are writing Hack code, you will typically start your file with `<?hh` and start writing code. However, that one top line is actually quite important in how the typechecker interprets your code.

## Partial Mode

Code in Hack files starting with the four characters `<?hh` at the top of it is said to be in *partial* mode. This means that the typechecker will check whatever it can, but no more; it does not insist on full type coverage. Partial mode is great to use to start gradually typing existing code. Here are the rules for partial mode:

- You can fully interoperate with PHP files -- you can call functions and use classes that the typechecker cannot see. This code is assumed to exist in a `<?php` file somewhere. See [the discussion of the `assume_php` configuration option](setup.md#common-configuration-options__assume_php) for a discussion of the implications of this, why it might not be desirable, and how to change it.
- You can write code at the very top-level (i.e., outside functions and methods), but it is not typechecked. To maximize the typechecker's ability to check code in partial mode, it is common practice to wrap all top-level code into one main function and have a call to that function be the only code at top-level.
- You can use references `&`, but the typechecker doesn't attempt to check them (for the purposes of typechecking, it ignores the `&`). Try not to use references since you can easily use them to break the type system.
- You can access superglobals without error.

@@ modes-examples/partial.php.type-errors @@
@@ modes-examples/non-hack-code.php @@

Notice that we have annotated some of the code, but not all of it. [Inferred code](../types/inference.md) is checked regardless of annotations on the function itself.

## Strict Mode

Hack files starting with:

```
<?hh // strict
```

means that the typechecker enforces strict typing rules in that file. If at all possible, start new projects with strict mode -- if every file in a codebase is in strict mode, the typechecker's coverage will be maximized since it will be able to fully check everything, and there can be no type errors at runtime.

Here are the rules for strict mode:

- All functions and methods must be fully annotated, i.e., their parameters and return types must be fully specified. Any location that can have a type annotation must have one.
- All functions called and classes referenced must be defined in a Hack file. Thus, for example, if your strict mode code tries to use a class defined in a `<?php` file, you will get an error because the typechecker doesn't look in `<?php` files and won't know about that class. However, from strict mode, you *can* call into Hack code in partial and decl mode.
- The only code allowed at the top-level are `require`, `require_once`, `include`, `include_once`, `namespace`, `use`, and definitions of classes, functions, [enums](/hack/enums/introduction), and constants.
- No references `&` anywhere.
- No accessing superglobals.

Strict is the mode you want to strive to. The entire typechecker goodness is at your disposal and should ensure zero runtime type errors.

@@ modes-examples/strict.php.type-errors @@
@@ modes-examples/non-hack-code.php @@

Notice that we cannot call into the `<?php` file any longer and that all entities in the Hack file are annotated.

## Decl Mode

Hack code starting with

```
<?hh // decl
```

is in decl mode. Decl mode code is not typechecked. However, the *signatures* of functions, classes, etc in decl mode are extracted, and are used by the typechecker when checking other code. Decl mode is most useful when converting over code written in PHP: although the body of that code might have type errors that you don't want to deal with right away, it's still beneficial to make the signature of that code, or at the very least its mere *existence*, visible to other code. In fact, one very basic migration path to Hack is to just change all your `<?php` files to decl mode, then start taking each file one-by-one and making them partial.

New Hack code should **never** be written in decl mode.

@@ modes-examples/decl.inc.php @@

@@ modes-examples/call-into-decl.php @@

@@ modes-examples/main-function.inc.php @@

*Output*

```
string(2) "ab"
```

The example shows all three modes. First it shows a decl mode file that used to be in `<?php`. Nothing else was added except the header change. Then it shows a strict mode file calling into the decl mode file. The typechecker knows the signatures of the functions and classes and can ensure basic things like whether you are calling a named entity and passing the right number of arguments. Finally, we have a partial mode file that actually calls the function in strict mode because we cannot have top-level function calls in strict mode.

## Mixing Modes

As shown in the example above, modes can be freely mixed; each file in your project can be in a different typechecker mode.
