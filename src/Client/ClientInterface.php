<?php

namespace Inkl\PlentyApi\Client;

interface ClientInterface {

    public function call($method, $params = []);

}
