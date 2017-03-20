<?php

namespace Perfumer\Component\Contracts\Example\Contract\Controller;

use Perfumer\Component\Contracts\Annotations\Ancestor;
use Perfumer\Component\Contracts\Annotations\Call;
use Perfumer\Component\Contracts\Annotations\Collection;
use Perfumer\Component\Contracts\Annotations\Context;
use Perfumer\Component\Contracts\Annotations\Custom;
use Perfumer\Component\Contracts\Annotations\Errors;
use Perfumer\Component\Contracts\Annotations\Extend;
use Perfumer\Component\Contracts\Annotations\Output;
use Perfumer\Component\Contracts\Annotations\Property;
use Perfumer\Component\Contracts\Annotations\Service;
use Perfumer\Component\Contracts\Annotations\Template;
use Perfumer\Component\Contracts\Annotations\Validate;

/**
 * @Extend(class="\Perfumer\Component\Contracts\Example\ParentController")
 * @Context(name="validators", class="\Perfumer\Component\Contracts\Example\Context\FooContext")
 */
interface FooController
{
    /**
     * @Validate (name="validators", method="intType", args={"param1"}, return="param1_valid")
     * @Validate (name="validators", method="intType", args={"param2"}, return="param2_valid", if="param1_valid")
     * @Collection(steps={
     *   @Call    (name="validators", method="sum",              args={"param1", "param2"},                   return=@Property("sum")),
     *   @Custom  (                   method="sumDoubled",       args={@Property("sum")},                     return="double_sum"),
     *   @Ancestor(                   method="sandboxActionTwo", args={@Property("sum"), @Property("staff")}, return={"sand", "box"}),
     *   @Service (name="foobar",     method="baz",              args={"sand", "box"},                        return=@Output)
     * })
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function bar(int $param1, int $param2);

    /**
     * @Validate(name="validators", method="intType",          args={"param1"},                  return="param1_valid")
     * @Validate(name="validators", method="intType",          args={"param2"},                  return="param2_valid")
     * @Call    (name="validators", method="sum",              args={"param1", "param2"},        return = "sum")
     * @Ancestor(                   method="sandboxActionTwo", args={"sum", @Property("staff")}, return = "sandbox")
     * @Service (name="foobar",     method="baz",              args={"sandbox"},                 return = @Output)
     * @Errors  (name="validators", method="fooErrors",        args={"param1_valid", "param2_valid"})
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function baz(int $param1, int $param2);
}
