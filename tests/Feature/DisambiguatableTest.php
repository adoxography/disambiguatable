<?php
/**
 * Handles tests for the Disambiguatable trait
 *
 * PHP version 7
 *
 * @category Test
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */

namespace Adoxography\Disambiguatable\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Adoxography\Disambiguatable\Disambiguation;
use Adoxography\Disambiguatable\Disambiguatable;
use Adoxography\Disambiguatable\Tests\TestCase;

/**
 * Defines all of the tests for the Disambiguatable trait
 *
 * @category Test
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */
class DisambiguatableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Called before each test is run
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->testModel = new class extends Model {
            use Disambiguatable;

            public $table = 'dummies';
            protected $fillable = ['field_1', 'field_2', 'field_3'];
            protected $disambiguatableFields = ['field_1', 'field_2'];
        };
    }

    /** @test */
    public function usingClassesMustDefineDisambiguatableFields()
    {
        $this->expectException(\InvalidArgumentException::class);

        new class extends Model {
            use Disambiguatable;
        };
    }

    /** @test */
    public function disambiguatorDefaultsToNull()
    {
        $this->testModel->create();

        $model = $this->testModel->first();
        $this->assertNull($model->disambiguator);
    }

    /** @test */
    public function disambiguatorDefaultsTo0IfAlwaysDisambiguateIsTrue()
    {
        $testModel = new class extends Model {
            use Disambiguatable;

            public $table = 'dummies';
            protected $alwaysDisambiguate = true;
            protected $fillable = ['field_1', 'field_2', 'field_3'];
            protected $disambiguatableFields = ['field_1', 'field_2'];
        };

        $testModel->create();

        $model = $testModel->first();
        $this->assertSame(0, $model->disambiguator);
    }

    /** @test */
    public function disambiguatorIsAssignedWhenDuplicatesExist()
    {
        $data = ['field_1' => 'Foo', 'field_2' => 'Bar'];
        $this->testModel->create($data);
        $this->testModel->create($data);

        $models = $this->testModel->all();

        $this->assertSame(0, $models->get(0)->disambiguator);
        $this->assertSame(1, $models->get(1)->disambiguator);
    }

    /** @test */
    public function modelsHaveToHaveIdenticalColumnsToBeDuplicates()
    {
        $this->testModel->create(['field_1' => 'Foo', 'field_2' => 'Bar']);
        $this->testModel->create(['field_1' => 'Foo', 'field_2' => 'Baz']);

        $models = $this->testModel->all();

        $this->assertNull($models->get(0)->disambiguator);
        $this->assertNull($models->get(1)->disambiguator);
    }

    /** @test */
    public function disambiguatorIsDeletedWhenDuplicateIsDestroyed()
    {
        $data = ['field_1' => 'Foo', 'field_2' => 'Bar'];
        $this->testModel->create($data);
        $this->testModel->create($data);
        $this->testModel->destroy(2);

        $source = $this->testModel->first();
        $disambiguations = Disambiguation::all();

        $this->assertNull($source->disambiguator);
        $this->assertCount(0, $disambiguations);
    }

    /** @test */
    public function disambiguatablesAreRenumberedOnDelete()
    {
        $data = ['field_1' => 'Foo', 'field_2' => 'Bar'];
        $this->testModel->create($data);
        $this->testModel->create($data);
        $this->testModel->create($data);

        $this->testModel->destroy(1);

        $models = $this->testModel->all();

        $this->assertSame(0, $models->get(0)->disambiguator);
        $this->assertSame(1, $models->get(1)->disambiguator);
    }

    /** @test */
    public function itRunsACallbackAfterDisambiguating()
    {
        $callbackModel = new class extends Model {
            use Disambiguatable;

            public $table = 'dummies';
            protected $fillable = ['field_1', 'field_2', 'field_3'];
            protected $disambiguatableFields = ['field_1', 'field_2'];

            public $callbackCalled = false;

            public function afterDisambiguated()
            {
                $this->field_3 = 'called';
                $this->save();
            }
        };

        $data = ['field_1' => 'Foo', 'field_2' => 'Bar'];
        $model1 = $callbackModel->create($data);
        $model2 = $callbackModel->create($data);

        $this->assertEquals('called', $model1->fresh()->field_3);
        $this->assertEquals('called', $model2->fresh()->field_3);
    }
}
