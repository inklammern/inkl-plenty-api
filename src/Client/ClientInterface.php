<?php

namespace Plenty\Api\Client;

interface ClientInterface {

    public function call($method, $params = []);

}