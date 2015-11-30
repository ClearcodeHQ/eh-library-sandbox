<?php

$app->post('/books', "app.controller:postBooksAction")->bind('post_books');
$app->get('/books', "app.controller:getBooksAction")->bind('get_books');
$app->post('/reservations', "app.controller:postReservationsAction")->bind('post_reservations');
$app->get('/reservations', "app.controller:getReservationsAction")->bind('get_reservations');
$app->put('/reservations', "app.controller:putReservationsAction")->bind('put_reservations');
$app->delete('/reservations', "app.controller:deleteReservationsAction")->bind('delete_reservations');
