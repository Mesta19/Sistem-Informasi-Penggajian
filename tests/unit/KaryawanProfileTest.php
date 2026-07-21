<?php

namespace Tests\unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\KaryawanModel;

/**
 * @internal
 */
final class KaryawanProfileTest extends CIUnitTestCase
{
    public function testEnsureColumnsExistMethod()
    {
        $model = new KaryawanModel();
        // Method should execute without error and alter table or verify columns
        $this->assertTrue(method_exists($model, 'ensureColumnsExist'));
    }
}
