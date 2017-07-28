Barman is a tool for generating php classes. It uses [zend-code](https://github.com/zendframework/zend-code) to generate class and [Doctrine Annotations](https://github.com/doctrine/annotations) to add annotations which mutate class and method generators in needed way.

Barman provides DSL (based on annotations) to describe method execution order and generate unit tests.

Built-in annotations
====================

- Alias - replaces string variables in method scope with Variable Annotations
- Context - defines context class or call context object if used as Step
- Custom - defines call of custom method in contract class
- Error - call method of Context if validation fails
- Inject - replaces string variables with Variable Annotations (placed in Context method Annotation scope)
- Injection - defines injected class or primitive or call injected object if used as Step
- Output - this variable will be returned by method
- Property - use class property as variable
- ServiceObject - call method of object in method scope (i.e. $some_object->someMethod())
- ServiceParent - call method of parent class (i.e. parent::someMethod())
- ServiceProperty - call method of class property (i.e. $this->some_property->someMethod())
- ServiceSelf - call static method of the class (i.e. self::someMethod())
- ServiceStatic - call static method of some class (i.e. SomeClass::someMethod())
- ServiceThis - call method from the class (i.e. $this->someMethod())
- Test - generate unit test for this method

Annotation fields
=================

- Alias
    * name {string} - name of variable in scope
    * variable {Annotation} - object annotation that will replace a variable
- Context (extends Step)
    * name {string} - short name of the context
    * class {string} - fully qualified name of the class
- Custom (extends Step)
- Error (extends Context)
- Inject
    * name {string} - name of argument in Context method
    * variable {Annotation} - object annotation that will replace an argument
- Injection (extends Step)
    * name {string} - short name of the injection
    * type {string} - fully qualified name of the class or primitive type
- Output
- Property
    * name {string} - name of class property
- ServiceObject (extends Step)
    * name {string} - name of object in scope
- ServiceParent (extends Step)
- ServiceProperty (extends Step)
    * name {string} - name of class property
- ServiceSelf (extends Step)
- ServiceStatic (extends Step)
    * name {string} - fully qualified name of the class
- ServiceThis (extends Step)
- Step
    * method {string} - name of method of the called object
    * arguments {array} - arguments passed to method
    * return {string|array} - returned variable names
    * if {string} - step proceeds, if this is true
    * unless {string} - step proceeds, if this is false
- Test

Lifecycle (creating own annotations)
====================================

- Generators are set to class annotations.
- onCreate() is called on class annotations.
- Class annotations implemented ClassAnnotationMutator mutate class annotations.
- onMutate() is called on class annotations.
- Generators are set to method annotations.
- onCreate() is called on method annotations.
- Class annotations implemented MethodAnnotationMutator mutate method annotations.
- Method annotations implemented MethodAnnotationMutator mutate method annotations.
- onMutate() is called on method annotations.
- Method annotations implemented StepGeneratorMutator mutate step generators.
- Class annotations implemented StepGeneratorMutator mutate step generators.
- Class annotations implemented MethodGeneratorMutator mutate method generators.
