<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Contract\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testSamplesAreValid(): void
    {
        self::assertSame([], $this->validator->errors(Samples::page('en')));
        self::assertSame([], $this->validator->errors(Samples::page('fa')));
        self::assertSame([], $this->validator->errors(Samples::demo('en')));
    }

    public function testRejectsUnknownTopLevelKey(): void
    {
        $document = Samples::page();
        $document['surprise'] = 'value';
        $errors = $this->validator->errors($document);
        self::assertArrayHasKey('document.surprise', $errors);
    }

    public function testRejectsUnknownComponentProperty(): void
    {
        $document = Samples::page();
        $document['sections'][0]['components'][0]['props']['onclick'] = 'alert(1)';
        $errors = $this->validator->errors($document);
        self::assertNotEmpty($errors);
        self::assertStringContainsString('props.onclick', implode(' ', array_keys($errors)));
    }

    public function testRejectsVariantOutsideRegistry(): void
    {
        $document = Samples::page();
        $document['sections'][0]['components'][0]['variant'] = 'invented';
        self::assertNotEmpty($this->validator->errors($document));
    }

    public function testRejectsUnknownComponentType(): void
    {
        $document = Samples::page();
        $document['sections'][0]['components'][0]['type'] = 'legacy-widget';
        self::assertNotEmpty($this->validator->errors($document));
    }

    public function testRejectsMissingRequiredProperty(): void
    {
        $document = Samples::page();
        unset($document['sections'][0]['components'][0]['props']['heading']);
        self::assertNotEmpty($this->validator->errors($document));
    }

    public function testRejectsJavascriptUrl(): void
    {
        $document = Samples::page();
        $document['sections'][0]['components'][0]['props']['primaryCta'] = ['label' => 'Go', 'href' => 'javascript:alert(1)'];
        self::assertNotEmpty($this->validator->errors($document));
    }

    public function testRejectsUnsafeDesignToken(): void
    {
        $document = Samples::page();
        $document['theme']['tokens']['colors']['light']['brand'] = 'red;}</style><script>alert(1)</script>';
        self::assertNotEmpty($this->validator->errors($document));
    }

    public function testRejectsEmptyVisibility(): void
    {
        $document = Samples::page();
        $document['sections'][0]['visibility']['devices'] = [];
        self::assertNotEmpty($this->validator->errors($document));
    }

    public function testRejectsWrongSchemaVersion(): void
    {
        $document = Samples::page();
        $document['schemaVersion'] = 2;
        self::assertNotEmpty($this->validator->errors($document));
    }

    public function testAssertThrowsWithFieldErrors(): void
    {
        $document = Samples::page();
        unset($document['title']);
        try {
            $this->validator->assert($document);
            self::fail('Expected a validation exception.');
        } catch (\ExpertsAcademy\Studio\Problem $problem) {
            self::assertSame(422, $problem->status);
            self::assertNotEmpty($problem->errors);
        }
    }

    public function testAcceptsValidRobotsDirective(): void
    {
        $document = Samples::page();
        $document['seo']['robots'] = 'noindex,nofollow';
        self::assertSame([], $this->validator->errors($document));
    }

    public function testRejectsUnknownRobotsDirective(): void
    {
        $document = Samples::page();
        $document['seo']['robots'] = 'noarchive';
        self::assertArrayHasKey('document.seo.robots', $this->validator->errors($document));
    }
}
