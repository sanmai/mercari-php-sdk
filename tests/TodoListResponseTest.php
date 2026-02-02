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
