# Welcome to Inquizitarium !!!

`Inquizitarium` - is a platform for creating survival quests with a built-in Quiz system.

Check the [How to Play?](docs/how-to-pay.md) guide to get started and have some FUN!

---

## OpenAPI

There is OpenAPI documentation (Swagger) available for the project.

Once you are [set up](docs/how-to-pay.md), navigate to `/swagger` url (by default https://localhost/swagger) to see it.

## Technical overview

This project is built as a `Modular Monolith` in adhere to [Domain Driven Design](https://en.wikipedia.org/wiki/Domain-driven_design) and using [Event Storming](https://en.wikipedia.org/wiki/Event_storming) modeling technique.

> NOTE: the understanding of DDD and Event Storming may differ from one person to another.
> This project is just my own sandbox to try out DDD in practice.
> It might be full of mistakes, misconceptions and cognitive distortions, so, please, treat it accordingly :)

There are 4 modules in `/src` directory

- `Core` - a base module containing common (shared) functionality used by other modules.
- `Math` - a module encapsulating Math domain (arithmetic expressions, operators, etc.)
- `Quiz` - a module representing the key application domain. This module contains 3 submodules:
  - `Creator` - a module responsible for creating/generating Quizzes
  - `Checker` - a module responsible for checking Quizzes
  - `ResultsStorage` - a module responsible for persisting Quiz Results provided by Quiz Checker module
- `Quests` - a module with all Quests using different UI (Cli, Web, etc.)
  - "The Shelter Demo" quest demonstrates a short simplified example how Quiz modules can be integrated into Web applications via Rest API
  - "The Dwarf Kingdom" quest implements CLI approach using a CommandBus to communicate with Quiz modules

_Dependency rules between modules are described in [deptrac-modules.yaml](deptrac-modules.yaml) file in project root._

_Dependency rules of DDD layers (Domain, Application, Infrastructure) are specified in [deptrac-layers.yaml](deptrac-layers.yaml)._

### Event Storming

Check the [Event Storming](docs/event-storming.md) page to know more about this technique and how it was used in this project. 
