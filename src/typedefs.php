<?hh // strict

namespace HHVM\UserDocumentation;

use FredEmmott\DefinitionFinder\ScannedBase;

type ScannedDefinitionFilter = (function(ScannedBase): bool);

enum DocumentationSourceType: string {
  FILE = 'file';
  ELF_SECTION = 'elf_section';
};

type DocumentationSource = shape(
  'type' => DocumentationSourceType,
  'name' => string,
  'mtime' => int,
);

type ClassDocumentation = shape(
  'name' => string,
  'methods' => array<string>,
);

type TypehintDocumentation = shape(
  'typename' => string,
  'genericTypes' => array<mixed /* cyclic: TypehintDocumentation*/>,
);

type GenericDocumentation = shape(
  'name' => string,
  'constraint' => ?string,
);

type FunctionDocumentation = shape(
  'name' => string,
  'returnType' => ?TypehintDocumentation,
  'generics' => array<GenericDocumentation>,
);

type DocumentationBundle = shape(
  'source' => DocumentationSource,
  'classes' => array<ClassDocumentation>,
  'interfaces' => array<ClassDocumentation>,
  'traits' => array<ClassDocumentation>,
  'functions' => array<FunctionDocumentation>,
);