<?php

namespace Clearcode\EHLibrarySandbox\Controller;

use Clearcode\EHLibrary\Application\UseCase\AddBookToLibrary;
use Clearcode\EHLibrary\Application\UseCase\CreateReservation;
use Clearcode\EHLibrary\Application\UseCase\GiveAwayBookInReservation;
use Clearcode\EHLibrary\Application\UseCase\GiveBackBookFromReservation;
use Clearcode\EHLibrary\Infrastructure\Persistence\LocalBookRepository;
use Clearcode\EHLibrary\Infrastructure\Persistence\LocalReservationRepository;
use Clearcode\EHLibrary\Infrastructure\Projection\LocalListOfBooksProjection;
use Clearcode\EHLibrary\Infrastructure\Projection\LocalListReservationsForBookProjection;
use Ramsey\Uuid\Uuid;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController
{
    /** @var Application  */
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return JsonResponse
     */
    public function postBooksAction()
    {
        $useCase = new AddBookToLibrary(new LocalBookRepository());
        $useCase->add(Uuid::uuid4()->toString(), 'Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum');

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @return JsonResponse
     */
    public function getBooksAction()
    {
        return new JsonResponse((new LocalListOfBooksProjection())->get());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postReservationsAction(Request $request)
    {
        $useCase = new CreateReservation(new LocalReservationRepository());
        $useCase->create(Uuid::fromString($request->request->get('bookId')), 'john@doe.com');

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getReservationsAction(Request $request)
    {
        return new JsonResponse((new LocalListReservationsForBookProjection())->get(Uuid::fromString($request->query->get('bookId'))));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function putReservationsAction(Request $request)
    {
        $useCase = new GiveAwayBookInReservation(new LocalReservationRepository());
        $useCase->giveAway($request->request->get('reservationId'));

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteReservationsAction(Request $request)
    {
        $useCase = new GiveBackBookFromReservation(new LocalReservationRepository());
        $useCase->giveBack($request->request->get('reservationId'));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
