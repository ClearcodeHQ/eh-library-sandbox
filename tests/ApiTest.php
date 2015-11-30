<?php

namespace tests\Clearcode\EHLibrarySandbox;

use Clearcode\EHLibrary\Infrastructure\Persistence\LocalBookRepository;
use Clearcode\EHLibrary\Infrastructure\Persistence\LocalReservationRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiTest extends WebTestCase
{
    /** @var UuidInterface */
    private static $bookId;
    /** @var UuidInterface */
    private static $reservationId;

    /** @test */
    public function it_can_add_book()
    {
        $this->visitRoute('POST', 'post_books');

        $this->assertThatResponseHasStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function it_can_get_list_of_books()
    {
        $this->visitRoute('GET', 'get_books');

        $this->assertThatResponseHasStatus(Response::HTTP_OK);
        $this->assertCount(1 , $this->jsonResponseData);

        self::$bookId = Uuid::fromString($this->jsonResponseData[0]['bookId']);
    }

    /** @test */
    public function it_can_add_reservation()
    {
        $this->visitRoute('POST', 'post_reservations', [], ['bookId' => self::$bookId->toString()]);

        $this->assertThatResponseHasStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function it_can_get_list_of_reservations()
    {
        $this->visitRoute('GET', 'get_reservations', ['bookId' => self::$bookId->toString()]);

        $this->assertThatResponseHasStatus(Response::HTTP_OK);
        $this->assertCount(1 , $this->jsonResponseData);

        self::$reservationId = Uuid::fromString($this->jsonResponseData[0]['reservationId']);
    }

    /** @test */
    public function it_can_give_away_reservation()
    {
        $this->visitRoute('PUT', 'put_reservations', [], ['reservationId' => self::$reservationId->toString()]);

        $this->assertThatResponseHasStatus(Response::HTTP_OK);
    }

    /** @test */
    public function it_give_back_reservation()
    {
        $this->visitRoute('DELETE', 'delete_reservations', [], ['reservationId' => self::$reservationId->toString()]);

        $this->assertThatResponseHasStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_can_get_list_of_reservations_again()
    {
        $this->visitRoute('GET', 'get_reservations', ['bookId' => self::$bookId->toString()]);

        $this->assertThatResponseHasStatus(Response::HTTP_OK);
        $this->assertCount(0 , $this->jsonResponseData);
    }

    public static function setUpBeforeClass()
    {
        (new LocalBookRepository())->clear();
        (new LocalReservationRepository())->clear();
    }
}
