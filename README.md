# phpDartObject

phpDartObject allows Dart developers to create Dart class representations in PHP that are validated, and then easily convertable into a format that can be safely serialized into a JSON format and passed to Dart.

## Usage

1. Extend IDartObject and create a valid object specification.
2. Create an instance of your type, initialize its values
3. Call instance->toResponse() on your instance
4. When ready to pass it to Dart, call response->toString()

### Object Specifications

An object specification a class with variables initialized at compile-time to a string containing the type of value that variable should hold.

Variables that start with an underscore are considered 'optional' variables, and do not necessarily need to be supplied with the object. Variables that start with two underscores are completely ignored and are not passed on to the request object.

Valid types are:
- string
- integer
- double
- boolean
- array
- NULL (Not entirely useful for specifications)
- subclasses of IDartObject

**Note: arrays may also only contain values that are one of the above valid types** 

### IDartObject->toResponse()

When you are ready to convert your object to a response, you call IDartObject->toResponse() on it. This will check your instance against its specification to ensure that it matches up both in variable requirements and types. All variables, required or optional, in the object are type-checked. If you have variables on your object that are not a part of the specification, they will be promptly ignored and not passed on to the response.

**Note: If a variable has been specified to be a subclass of IDartObject, setting it to NULL will effectively unset the variable from the class, causing object validation to fail if the variable is marked as required.** 

## TO DO

- [ ] De-serialize JSON into an IDartObject if dartObjectType is specified

