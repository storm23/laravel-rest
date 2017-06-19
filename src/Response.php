<?php
namespace Storm23\LaravelRest;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\MessageBag;

class Response
{
    protected static function basic($message, $httpCode = 200, array $errors = [])
    {
        $prefix = 'HTTP_' . $httpCode . ': ';
        return response()->json(array_filter([
            'message' => $prefix . $message,
            'errors' => $errors,
        ]), $httpCode);
    }

    public static function success($message = 'Success.')
    {
        return self::basic($message);
    }

    public static function redirect($url, $httpCode = 301)
    {
        $response = self::basic('Redirect.', $httpCode);
        return $response->header('Location', $url);
    }

    public static function notFound(array $errors = [])
    {
        return self::basic('Error.', 404, $errors);
    }

    public static function error($message = 'Error.', $httpCode = 500, array $errors = [])
    {
        return self::basic($message, $httpCode, $errors);
    }

    public static function object($data = [])
    {
        $eTag = md5($data);

        if (\Request::header('If-None-Match') === $eTag) {
            return self::basic('Not modified.', 304);
        }

        $response = isset($data[0]) ?
            response()->json($data)->header('Count', count($data)) :
            response()->json($data);

        return $response->header('ETag', $eTag);
    }

    public static function paginate(LengthAwarePaginator $paginator)
    {
        $paginator->appends('size', $paginator->perPage());

        $data = $paginator->items();
        $eTag = md5(serialize($data));
        if (\Request::header('If-None-Match') === $eTag) {
            return self::basic('Not modified.', 304);
        }

        $links = [

            'next' => $paginator->nextPageUrl(),
            'prev' => $paginator->previousPageUrl(),
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage())
        ];
        $values = [];
        foreach ($links as $type => $url) {

            if (!empty($url)) {

                $values[] = sprintf('<%s>; rel="%s"', $url, $type);
            }
        }
        $links = implode(', ', $values);

        $headers = [

            'Paginator' => sprintf('%s/%s/%s', $paginator->perPage(), $paginator->currentPage(), $paginator->total()),
            'Links' => $links,
            'ETag' => $eTag,
        ];

        return response()->json($data)->withHeaders($headers);
    }

    public static function invalid(MessageBag $errors)
    {
        return self::error('Request not valid.', 400, $errors->toArray());
    }
}
