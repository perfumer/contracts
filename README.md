Lifecycle (creating own annotations)
====================================

- Generators are set to class annotations.
- onCreate() is called on class annotations.
- Class annotations implemented ClassAnnotationDecorator decorate class annotations.
- onDecorate() is called on class annotations.
- Generators are set to method annotations.
- onCreate() is called on method annotations.
- Class annotations implemented MethodAnnotationDecorator decorate method annotations.
- Method annotations implemented MethodAnnotationDecorator decorate method annotations.
- onDecorate() is called on method annotations.
- Method annotations implemented StepGeneratorDecorator decorate step generators.
- Class annotations implemented StepGeneratorDecorator decorate step generators.
- Class annotations implemented MethodGeneratorDecorator decorate method generators.
