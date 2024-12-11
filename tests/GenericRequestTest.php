<?php

/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko
 *
 * Mercari PHP SDK is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or (at your option) any later version.
 *
 * Mercari PHP SDK is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Mercari PHP SDK. If not, see <https://www.gnu.org/licenses/>.
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
