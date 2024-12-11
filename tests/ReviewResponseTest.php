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

use Mercari\ReviewResponse;

/**
 * @covers \Mercari\ReviewResponse
 */
class ReviewResponseTest extends TestCase
{
    public function testSuccess()
    {
        $response = new ReviewResponse();
        $response->review_status = $response::SUCCESS;

        $this->assertTrue($response->isSuccess());

    }

    public function testFailure()
    {
        $response = new ReviewResponse();
        $response->review_status = '';

        $this->assertFalse($response->isSuccess());
    }
}
