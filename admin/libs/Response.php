<?php
/**
 * Response Handler
 * Standardize API responses
 */
class Response
{
    public static function success($data = null, $message = 'Success', $code = 200)
    {
        http_response_code($code);
        return json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($message = 'Error', $code = 400, $errors = null)
    {
        http_response_code($code);
        $response = [
            'success' => false,
            'message' => $message
        ];
        if ($errors) $response['errors'] = $errors;
        return json_encode($response);
    }

    public static function paginate($data, $total, $page, $limit)
    {
        http_response_code(200);
        return json_encode([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
}
?>