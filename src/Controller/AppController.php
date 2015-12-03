<?php

namespace Clearcode\EHLibrarySandbox\Silex\Controller;

use Clearcode\EHLibrary\Application;
use Clearcode\EHLibrary\Infrastructure\Projection\LocalListReservationsForBookProjection;
use Clearcode\EHLibrary\Library;
use Clearcode\EHLibrary\Model\BookInReservationAlreadyGivenAway;
use Clearcode\EHLibrary\Model\CannotGiveBackReservationWhichWasNotGivenAway;
use Ramsey\Uuid\Uuid;
use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController
{
    /** @var Library */
    private $library;

    public function __construct()
    {
        $this->library = new Application();
    }

    /**
     * @param Request $request
     * @param string  $bookId
     * @return JsonResponse
     */
    public function putBooksAction(Request $request, $bookId)
    {
        if (null === $request->request->get('title') || null === $request->request->get('authors') || null === $request->request->get('isbn')) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $this->library->addBook(Uuid::fromString($bookId), $request->request->get('title'), $request->request->get('authors'), $request->request->get('isbn'));

        return new JsonResponse(['id' => $bookId], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBooksAction(Request $request)
    {
        return new JsonResponse($this->library->listOfBooks($request->query->get('page', 1), $request->query->get('booksPerPage')));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postReservationsAction(Request $request)
    {
        if (null === $request->request->get('email') || null === $request->request->get('bookId')) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $reservationId = Uuid::uuid4();

        $this->library->createReservation($reservationId, Uuid::fromString($request->request->get('bookId')), $request->request->get('email'));

        return new JsonResponse(['id' => $reservationId], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param         $reservationId
     * @return JsonResponse
     */
    public function patchReservationsAction(Request $request, $reservationId)
    {
        if (null === $request->request->get('givenAwayAt')) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->library->giveAwayBookInReservation(Uuid::fromString($reservationId), new \DateTime($request->request->get('givenAwayAt')));
        } catch (BookInReservationAlreadyGivenAway $e) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @param $reservationId
     * @return Response
     */
    public function deleteReservationsAction($reservationId)
    {
        try {
            $this->library->giveBackBookFromReservation(Uuid::fromString($reservationId));
        } catch (CannotGiveBackReservationWhichWasNotGivenAway $e) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getReservationsAction(Request $request)
    {
        if (null == $bookId = $request->query->get('bookId')) {
            return new JsonResponse([]);
        }

        return new JsonResponse((new LocalListReservationsForBookProjection())->get(Uuid::fromString($bookId)));
    }
}
