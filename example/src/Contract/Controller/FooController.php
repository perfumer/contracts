<?php

namespace Perfumer\Component\Contracts\Example\Contract\Controller;

use Perfumer\Component\Contracts\Annotations\Alias;
use Perfumer\Component\Contracts\Annotations\Call;
use Perfumer\Component\Contracts\Annotations\Collection;
use Perfumer\Component\Contracts\Annotations\Context;
use Perfumer\Component\Contracts\Annotations\Custom;
use Perfumer\Component\Contracts\Annotations\Error;
use Perfumer\Component\Contracts\Annotations\Extend;
use Perfumer\Component\Contracts\Annotations\Inject;
use Perfumer\Component\Contracts\Annotations\Output;
use Perfumer\Component\Contracts\Annotations\Property;
use Perfumer\Component\Contracts\Annotations\ServiceObject;
use Perfumer\Component\Contracts\Annotations\ServiceParent;
use Perfumer\Component\Contracts\Annotations\ServiceProperty;
use Perfumer\Component\Contracts\Annotations\Skip;
use Perfumer\Component\Contracts\Annotations\Template;

/**
 * @Extend(class="\Perfumer\Component\Contracts\Example\ParentController")
 * @Context(name="validators_lib", class="\Perfumer\Component\Contracts\Example\Context\FooContext")
 * @Inject(name="iterator", type="\Iterator")
 * @Inject(name="foo", type="\Perfumer\Component\Contracts\Example\FooService")
 * @Inject(name="some_string", type="string")
 */
interface FooController
{
    /**
     * @Call (name="validators_lib", method="intType", arguments={"a"}, return="a_valid")
     * @Call (name="validators_lib", method="intType", arguments={"a"}, return="param2_valid", if="a_valid")
     * @Call (name="foo", method="bar", if="a_valid")
     * @Collection(steps={
     *   @Call           (name="validators_lib", method="sum",                                        return="a"),
     *   @Custom         (                       method="sumDoubled",       arguments={"a"},          return="double_sum"),
     *   @ServiceParent  (                       method="sandboxActionTwo", arguments={"a", "staff"}, return={"sand", "box"}),
     *   @ServiceProperty(name="foobar",         method="baz",              arguments={"a", "box"},   return=@Output)
     * })
     * @Error (name="validators_lib", method="fooErrors", unless="a_valid")
     * @Error (name="validators_lib", method="fooErrors", unless="param2_valid")
     *
     * @Alias(name="a",     variable=@Property("a"))
     * @Alias(name="staff", variable=@Property("staff"))
     * @Alias(name="box",   variable=@Property("box"))
     *
     * @param Output $param2
     * @return string
     */
    public function barAction(Output $param2): string;

    /**
     * @Call           (name="validators_lib", method="intType",          arguments={"param1"},                  return="param1_valid")
     * @Call           (name="validators_lib", method="intType",          arguments={"param2"},                  return="param2_valid", if="param1_valid")
     * @Call           (name="validators_lib", method="sum",              arguments={"param1"},                  return="sum")
     * @ServiceParent  (                   method="sandboxActionTwo", arguments={"sum", @Property("staff")}, return="sandbox")
     * @ServiceProperty(name="foobar",     method="baz",              arguments={@Context("validators_lib")})
     * @ServiceObject  (name="sandbox",    method="execute")
     *
     * @param int $param1
     * @param int $param2
     * @return \DateTime
     */
    public function bazAction(int $param1, int $param2);

    /**
     * @Skip()
     */
    public function skipped();
}
