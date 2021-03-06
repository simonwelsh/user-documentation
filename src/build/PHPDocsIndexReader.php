<?hh // strict

namespace HHVM\UserDocumentation;

final class PHPDocsIndexReader {
  private Map<string, string> $classes = Map { };
  private Map<string, string> $functions = Map { };
  private Map<string, string> $methods = Map { };

  public function __construct(
    string $content,
  ) {
    $old_index = json_decode($content);

    foreach ($old_index as $entry) {
      list ($name, $id, $type) = $entry;

      $name = html_entity_decode($name);

      if ($type === 'phpdoc:classref') {
        $name = explode('<', $name)[0]; // remove generics
        $this->classes[$name] = $id;
        continue;
      }
      if ($type !== 'refentry') {
        continue;
      }
      $parts = (new Vector(explode('::', $name)))
        ->map($x ==> explode('<', $x)[0]);

      if (count($parts) === 1) {
        $this->functions[$parts[0]] = $id;
        continue;
      }

      invariant(
        count($parts) === 2,
        "Definition %s has %d parts",
        $name,
        count($parts),
      );
      $this->methods[implode('::', $parts)] = $id;
    }
  }

  public function getClasses(): ImmMap<string, string> {
    return $this->classes->toImmMap();
  }

  public function getFunctions(): ImmMap<string, string> {
    return $this->functions->toImmMap();
  }

  public function getMethods(): ImmMap<string, string> {
    return $this->methods->toImmMap();
  }
}
