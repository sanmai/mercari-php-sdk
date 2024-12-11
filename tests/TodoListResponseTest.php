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

use Mercari\TodoListResponse;

/**
 * @covers \Mercari\TodoListResponse
 */
class TodoListResponseTest extends TestCase
{
    public function testDeserialize()
    {
        $file = __DIR__ . '/data/todolist.json';
        $response = $this->deserializeFile($file, TodoListResponse::class);

        /** @var TodoListResponse $response */
        $this->assertSame(123412345, $response->data[0]->created);
        $this->assertStringStartsWith('ご登録のメールアドレスに認証メールを送りました。', $response->data[0]->message);

        $this->assertDeserializedSame($file, $response);
    }

    public function testPostDeserialize()
    {
        $file = __DIR__ . '/data/todolist_null.json';

        $response = $this->deserializeFile($file, TodoListResponse::class);

        /** @var TodoListResponse $response */
        $this->assertSame([], $response->data);
    }
}
