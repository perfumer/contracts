<?php

namespace Perfumer\Component\Contracts\Example\Contract\Controller;

use Perfumer\Component\Contracts\Annotations\Call;
use Perfumer\Component\Contracts\Annotations\Collection;
use Perfumer\Component\Contracts\Annotations\Context;
use Perfumer\Component\Contracts\Annotations\Custom;
use Perfumer\Component\Contracts\Annotations\Errors;
use Perfumer\Component\Contracts\Annotations\Extend;
use Perfumer\Component\Contracts\Annotations\Service;
use Perfumer\Component\Contracts\Annotations\Template;
use Perfumer\Component\Contracts\Annotations\Validate;

/**
 * @Extend(class = "\Perfumer\Component\Contracts\Example\ParentController")
 * @Context(name = "validators", class = "\Perfumer\Component\Contracts\Example\Context\FooContext")
 */
interface FooController
{
    /**
     * @Validate (name = "validators", method = "intType",          arguments = {"param1"},            return = "param1_valid")
     * @Validate (name = "validators", method = "intType",          arguments = {"param2"},            return = "param2_valid", if = "param1_valid")
     * @Collection(steps = {
     *   @Call   (name = "validators", method = "sum",              arguments = {"param1", "param2"},  return = "sum"),
     *   @Custom (                     method = "sumDoubled",       arguments = {"sum"},               return = "double_sum"),
     *   @Service(name = "_parent",    method = "sandboxActionTwo", arguments = {"sum", "this.staff"}, return = {"sand", "box"}),
     *   @Service(name = "foobar",     method = "baz",              arguments = {"sand", "box"},       return = "_return")
     * })
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function bar(int $param1, int $param2);

    /**
     * @Validate(name = "validators", method = "intType",          arguments = {"param1"},            return="param1_valid")
     * @Validate(name = "validators", method = "intType",          arguments = {"param2"},            return="param2_valid")
     * @Call    (name = "validators", method = "sum",              arguments = {"param1", "param2"},  return = "sum")
     * @Service (name = "_parent",    method = "sandboxActionTwo", arguments = {"sum", "this.staff"}, return = "sandbox")
     * @Service (name = "foobar",     method = "baz",              arguments = {"sandbox"},           return = "_return")
     * @Errors  (name = "validators", method = "fooErrors",        arguments = {"param1_valid", "param2_valid"})
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function baz(int $param1, int $param2);
}
