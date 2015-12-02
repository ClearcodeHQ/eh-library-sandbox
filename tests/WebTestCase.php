<?php

namespace tests\Clearcode\EHLibrarySandbox\Silex;

use Clearcode\EHLibrary\Infrastructure\Persistence\LocalBookRepository;
use Clearcode\EHLibrary\Infrastructure\Persistence\LocalReservationRepository;
use Clearcode\EHLibrary\Model\Book;
use Clearcode\EHLibrary\Model\Reservation;
use Ramsey\Uuid\Uuid;
use Silex\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpKernel\Client;

abstract class WebTestCase extends BaseWebTestCase
{
    /** @var Client */
    protected $client;
    /** @var array */
    protected $jsonResponseData;
    /** @var LocalBookRepository */
    private $books;
    /** @var LocalReservationRepository */
    private $reservations;

    /** {@inheritdoc} */
    public function setUp()
    {
        parent::setUp();

        $this->client = $this->createClient();

        $this->books = new LocalBookRepository();
        $this->reservations = new LocalReservationRepository();

        $this->clearDatabase();
    }

    /** {@inheritdoc} */
    public function createApplication()
    {
        $app = require __DIR__.'/../src/app.php';

        require __DIR__.'/../config/dev.php';
        require __DIR__.'/../config/routing.php';

        return $app;
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->client = null;
        $this->jsonResponseData = null;

        $this->books = null;
        $this->reservations = null;

        parent::tearDown();
    }

    protected function addBook($bookId, $title, $authors, $isbn)
    {
        $this->books->save(new Book(Uuid::fromString($bookId), $title, $authors, $isbn));
    }

    protected function addReservation($reservationId, $bookId, $givenAway = false)
    {
        $reservation = new Reservation(Uuid::fromString($reservationId), Uuid::fromString($bookId), 'john@doe.com');

        if ($givenAway) {
            $reservation->giveAway();
        }

        $this->reservations->save($reservation);
    }

    protected function request($method, $url, $requestParameters = array())
    {
        $this->client->request($method, $url, $requestParameters);

        $this->jsonResponseData = json_decode((string) $this->client->getResponse()->getContent(), true);
    }

    protected function assertThatResponseHasStatus($expectedStatus)
    {
        $this->assertEquals($expectedStatus, $this->client->getResponse()->getStatusCode());
    }

    protected function assertThatResponseHasContentType($expectedContentType)
    {
        $this->assertContains($expectedContentType, $this->client->getResponse()->headers->get('Content-Type'));
    }

    protected function assertThatResponseHasNotContentType()
    {
        $this->assertEmpty($this->client->getResponse()->headers->get('Content-Type'));
    }

    private function clearDatabase()
    {
        $this->books->clear();
        $this->reservations->clear();
    }
}
