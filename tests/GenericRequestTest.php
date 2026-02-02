<?php

/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko <alexey@kopytko.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Tests\Mercari;

use Tests\Mercari\Doubles\ExampleRequest;

/**
 * @covers \Mercari\GenericRequest
 */
class GenericRequestTest extends TestCase
{
    public function testRequest()
    {
        $data = [
            'foo' => 1,
            'bar' => 2,
        ];

        $request = new ExampleRequest($data);

        $this->assertNull($request->baz);

        $this->assertSame(1, $request->foo);

        $request->foo = 5;

        $this->assertSame(5, $request->foo);

        $this->assertSame([
            'foo' => 5,
            'bar' => 2,
        ], $request->getRequestParams());

        $request->zap = 'test';

        $this->assertSame('{"foo":5,"bar":2,"zap":"test"}', json_encode($request));
    }
}
