<?php
/**
 * PostApi
 * PHP version 5
 *
 * @category Class
 * @package  Listae\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * listae API 2.0
 *
 * Documentación de los servicios REST de listae
 *
 * OpenAPI spec version: 2.0.1
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.23
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Listae\Client\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Listae\Client\ApiException;
use Listae\Client\Configuration;
use Listae\Client\HeaderSelector;
use Listae\Client\ObjectSerializer;

/**
 * PostApi Class Doc Comment
 *
 * @category Class
 * @package  Listae\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class PostApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation getPost
     *
     * @param  int $post_id El identificador de la publicacion. (required)
     * @param  string $accept_language accept_language (optional, default to es)
     *
     * @throws \Listae\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Listae\Client\Model\Post
     */
    public function getPost($post_id, $accept_language = 'es')
    {
        list($response) = $this->getPostWithHttpInfo($post_id, $accept_language);
        return $response;
    }

    /**
     * Operation getPostWithHttpInfo
     *
     * @param  int $post_id El identificador de la publicacion. (required)
     * @param  string $accept_language (optional, default to es)
     *
     * @throws \Listae\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Listae\Client\Model\Post, HTTP status code, HTTP response headers (array of strings)
     */
    public function getPostWithHttpInfo($post_id, $accept_language = 'es')
    {
        $returnType = '\Listae\Client\Model\Post';
        $request = $this->getPostRequest($post_id, $accept_language);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Listae\Client\Model\Post',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getPostAsync
     *
     * 
     *
     * @param  int $post_id El identificador de la publicacion. (required)
     * @param  string $accept_language (optional, default to es)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPostAsync($post_id, $accept_language = 'es')
    {
        return $this->getPostAsyncWithHttpInfo($post_id, $accept_language)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getPostAsyncWithHttpInfo
     *
     * 
     *
     * @param  int $post_id El identificador de la publicacion. (required)
     * @param  string $accept_language (optional, default to es)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPostAsyncWithHttpInfo($post_id, $accept_language = 'es')
    {
        $returnType = '\Listae\Client\Model\Post';
        $request = $this->getPostRequest($post_id, $accept_language);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getPost'
     *
     * @param  int $post_id El identificador de la publicacion. (required)
     * @param  string $accept_language (optional, default to es)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function getPostRequest($post_id, $accept_language = 'es')
    {
        // verify the required parameter 'post_id' is set
        if ($post_id === null || (is_array($post_id) && count($post_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $post_id when calling getPost'
            );
        }

        $resourcePath = '/posts/detail/{post-id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // header params
        if ($accept_language !== null) {
            $headerParams['Accept-Language'] = ObjectSerializer::toHeaderValue($accept_language);
        }

        // path params
        if ($post_id !== null) {
            $resourcePath = str_replace(
                '{' . 'post-id' . '}',
                ObjectSerializer::toPathValue($post_id),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-listae-key');
        if ($apiKey !== null) {
            $headers['x-listae-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation searchPosts
     *
     * Búsqueda de publicaciones
     *
     * @param  string $accept_language accept_language (optional, default to es)
     * @param  string $r2q Texto libre de búsqueda (optional)
     * @param  string $r2l Texto libre de localización geográfica (optional)
     * @param  string $r2r Filtro de región (Provincia / Región / Estado) (optional)
     * @param  string $r2t Filtro de población (optional)
     * @param  string $r2c Filtro de país (optional)
     * @param  string $r2b Filtra publicaciones con negocio (valor True), sin negocio (valor False), sin filtrar (valor nulo) (optional)
     * @param  string $r2blog Filtro por blog de publicación, por ejemplo el valor “ http://example.listae.me/ ” sacaría solo publicaciones del sitio web de example (optional)
     * @param  string[] $r2cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
     * @param  string[] $r2tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
     * @param  float $r2lat Latitúd para buscar cerca de un punto gps (optional)
     * @param  float $r2lon Longitúd para buscar cerca de un punto gps (optional)
     * @param  int $r2dst Distancia en metros, radio del punto gps (optional)
     * @param  int $r2s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
     * @param  int $r2sc Número de elementos por página (optional, default to 10)
     * @param  string $r2bss identificadores de negocios separados por comas, por ejemplo “sample-1,sample-2” te sacaría solo publicaciones de los restaurantes Sample 1 y Sample 2 (optional)
     *
     * @throws \Listae\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \Listae\Client\Model\SearchPostFilter
     */
    public function searchPosts($accept_language = 'es', $r2q = null, $r2l = null, $r2r = null, $r2t = null, $r2c = null, $r2b = null, $r2blog = null, $r2cat = null, $r2tag = null, $r2lat = null, $r2lon = null, $r2dst = null, $r2s = null, $r2sc = '10', $r2bss = null)
    {
        list($response) = $this->searchPostsWithHttpInfo($accept_language, $r2q, $r2l, $r2r, $r2t, $r2c, $r2b, $r2blog, $r2cat, $r2tag, $r2lat, $r2lon, $r2dst, $r2s, $r2sc, $r2bss);
        return $response;
    }

    /**
     * Operation searchPostsWithHttpInfo
     *
     * Búsqueda de publicaciones
     *
     * @param  string $accept_language (optional, default to es)
     * @param  string $r2q Texto libre de búsqueda (optional)
     * @param  string $r2l Texto libre de localización geográfica (optional)
     * @param  string $r2r Filtro de región (Provincia / Región / Estado) (optional)
     * @param  string $r2t Filtro de población (optional)
     * @param  string $r2c Filtro de país (optional)
     * @param  string $r2b Filtra publicaciones con negocio (valor True), sin negocio (valor False), sin filtrar (valor nulo) (optional)
     * @param  string $r2blog Filtro por blog de publicación, por ejemplo el valor “ http://example.listae.me/ ” sacaría solo publicaciones del sitio web de example (optional)
     * @param  string[] $r2cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
     * @param  string[] $r2tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
     * @param  float $r2lat Latitúd para buscar cerca de un punto gps (optional)
     * @param  float $r2lon Longitúd para buscar cerca de un punto gps (optional)
     * @param  int $r2dst Distancia en metros, radio del punto gps (optional)
     * @param  int $r2s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
     * @param  int $r2sc Número de elementos por página (optional, default to 10)
     * @param  string $r2bss identificadores de negocios separados por comas, por ejemplo “sample-1,sample-2” te sacaría solo publicaciones de los restaurantes Sample 1 y Sample 2 (optional)
     *
     * @throws \Listae\Client\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \Listae\Client\Model\SearchPostFilter, HTTP status code, HTTP response headers (array of strings)
     */
    public function searchPostsWithHttpInfo($accept_language = 'es', $r2q = null, $r2l = null, $r2r = null, $r2t = null, $r2c = null, $r2b = null, $r2blog = null, $r2cat = null, $r2tag = null, $r2lat = null, $r2lon = null, $r2dst = null, $r2s = null, $r2sc = '10', $r2bss = null)
    {
        $returnType = '\Listae\Client\Model\SearchPostFilter';
        $request = $this->searchPostsRequest($accept_language, $r2q, $r2l, $r2r, $r2t, $r2c, $r2b, $r2blog, $r2cat, $r2tag, $r2lat, $r2lon, $r2dst, $r2s, $r2sc, $r2bss);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Listae\Client\Model\SearchPostFilter',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation searchPostsAsync
     *
     * Búsqueda de publicaciones
     *
     * @param  string $accept_language (optional, default to es)
     * @param  string $r2q Texto libre de búsqueda (optional)
     * @param  string $r2l Texto libre de localización geográfica (optional)
     * @param  string $r2r Filtro de región (Provincia / Región / Estado) (optional)
     * @param  string $r2t Filtro de población (optional)
     * @param  string $r2c Filtro de país (optional)
     * @param  string $r2b Filtra publicaciones con negocio (valor True), sin negocio (valor False), sin filtrar (valor nulo) (optional)
     * @param  string $r2blog Filtro por blog de publicación, por ejemplo el valor “ http://example.listae.me/ ” sacaría solo publicaciones del sitio web de example (optional)
     * @param  string[] $r2cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
     * @param  string[] $r2tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
     * @param  float $r2lat Latitúd para buscar cerca de un punto gps (optional)
     * @param  float $r2lon Longitúd para buscar cerca de un punto gps (optional)
     * @param  int $r2dst Distancia en metros, radio del punto gps (optional)
     * @param  int $r2s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
     * @param  int $r2sc Número de elementos por página (optional, default to 10)
     * @param  string $r2bss identificadores de negocios separados por comas, por ejemplo “sample-1,sample-2” te sacaría solo publicaciones de los restaurantes Sample 1 y Sample 2 (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function searchPostsAsync($accept_language = 'es', $r2q = null, $r2l = null, $r2r = null, $r2t = null, $r2c = null, $r2b = null, $r2blog = null, $r2cat = null, $r2tag = null, $r2lat = null, $r2lon = null, $r2dst = null, $r2s = null, $r2sc = '10', $r2bss = null)
    {
        return $this->searchPostsAsyncWithHttpInfo($accept_language, $r2q, $r2l, $r2r, $r2t, $r2c, $r2b, $r2blog, $r2cat, $r2tag, $r2lat, $r2lon, $r2dst, $r2s, $r2sc, $r2bss)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation searchPostsAsyncWithHttpInfo
     *
     * Búsqueda de publicaciones
     *
     * @param  string $accept_language (optional, default to es)
     * @param  string $r2q Texto libre de búsqueda (optional)
     * @param  string $r2l Texto libre de localización geográfica (optional)
     * @param  string $r2r Filtro de región (Provincia / Región / Estado) (optional)
     * @param  string $r2t Filtro de población (optional)
     * @param  string $r2c Filtro de país (optional)
     * @param  string $r2b Filtra publicaciones con negocio (valor True), sin negocio (valor False), sin filtrar (valor nulo) (optional)
     * @param  string $r2blog Filtro por blog de publicación, por ejemplo el valor “ http://example.listae.me/ ” sacaría solo publicaciones del sitio web de example (optional)
     * @param  string[] $r2cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
     * @param  string[] $r2tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
     * @param  float $r2lat Latitúd para buscar cerca de un punto gps (optional)
     * @param  float $r2lon Longitúd para buscar cerca de un punto gps (optional)
     * @param  int $r2dst Distancia en metros, radio del punto gps (optional)
     * @param  int $r2s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
     * @param  int $r2sc Número de elementos por página (optional, default to 10)
     * @param  string $r2bss identificadores de negocios separados por comas, por ejemplo “sample-1,sample-2” te sacaría solo publicaciones de los restaurantes Sample 1 y Sample 2 (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function searchPostsAsyncWithHttpInfo($accept_language = 'es', $r2q = null, $r2l = null, $r2r = null, $r2t = null, $r2c = null, $r2b = null, $r2blog = null, $r2cat = null, $r2tag = null, $r2lat = null, $r2lon = null, $r2dst = null, $r2s = null, $r2sc = '10', $r2bss = null)
    {
        $returnType = '\Listae\Client\Model\SearchPostFilter';
        $request = $this->searchPostsRequest($accept_language, $r2q, $r2l, $r2r, $r2t, $r2c, $r2b, $r2blog, $r2cat, $r2tag, $r2lat, $r2lon, $r2dst, $r2s, $r2sc, $r2bss);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'searchPosts'
     *
     * @param  string $accept_language (optional, default to es)
     * @param  string $r2q Texto libre de búsqueda (optional)
     * @param  string $r2l Texto libre de localización geográfica (optional)
     * @param  string $r2r Filtro de región (Provincia / Región / Estado) (optional)
     * @param  string $r2t Filtro de población (optional)
     * @param  string $r2c Filtro de país (optional)
     * @param  string $r2b Filtra publicaciones con negocio (valor True), sin negocio (valor False), sin filtrar (valor nulo) (optional)
     * @param  string $r2blog Filtro por blog de publicación, por ejemplo el valor “ http://example.listae.me/ ” sacaría solo publicaciones del sitio web de example (optional)
     * @param  string[] $r2cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
     * @param  string[] $r2tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
     * @param  float $r2lat Latitúd para buscar cerca de un punto gps (optional)
     * @param  float $r2lon Longitúd para buscar cerca de un punto gps (optional)
     * @param  int $r2dst Distancia en metros, radio del punto gps (optional)
     * @param  int $r2s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
     * @param  int $r2sc Número de elementos por página (optional, default to 10)
     * @param  string $r2bss identificadores de negocios separados por comas, por ejemplo “sample-1,sample-2” te sacaría solo publicaciones de los restaurantes Sample 1 y Sample 2 (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function searchPostsRequest($accept_language = 'es', $r2q = null, $r2l = null, $r2r = null, $r2t = null, $r2c = null, $r2b = null, $r2blog = null, $r2cat = null, $r2tag = null, $r2lat = null, $r2lon = null, $r2dst = null, $r2s = null, $r2sc = '10', $r2bss = null)
    {

        $resourcePath = '/search/posts';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        if ($r2q !== null) {
            $queryParams['r2q'] = ObjectSerializer::toQueryValue($r2q, null);
        }
        // query params
        if ($r2l !== null) {
            $queryParams['r2l'] = ObjectSerializer::toQueryValue($r2l, null);
        }
        // query params
        if ($r2r !== null) {
            $queryParams['r2r'] = ObjectSerializer::toQueryValue($r2r, null);
        }
        // query params
        if ($r2t !== null) {
            $queryParams['r2t'] = ObjectSerializer::toQueryValue($r2t, null);
        }
        // query params
        if ($r2c !== null) {
            $queryParams['r2c'] = ObjectSerializer::toQueryValue($r2c, null);
        }
        // query params
        if ($r2b !== null) {
            $queryParams['r2b'] = ObjectSerializer::toQueryValue($r2b, null);
        }
        // query params
        if ($r2blog !== null) {
            $queryParams['r2blog'] = ObjectSerializer::toQueryValue($r2blog, null);
        }
        // query params
        if (is_array($r2cat)) {
            $r2cat = ObjectSerializer::serializeCollection($r2cat, 'multi', true);
        }
        if ($r2cat !== null) {
            $queryParams['r2cat'] = ObjectSerializer::toQueryValue($r2cat, null);
        }
        // query params
        if (is_array($r2tag)) {
            $r2tag = ObjectSerializer::serializeCollection($r2tag, 'multi', true);
        }
        if ($r2tag !== null) {
            $queryParams['r2tag'] = ObjectSerializer::toQueryValue($r2tag, null);
        }
        // query params
        if ($r2lat !== null) {
            $queryParams['r2lat'] = ObjectSerializer::toQueryValue($r2lat, 'float');
        }
        // query params
        if ($r2lon !== null) {
            $queryParams['r2lon'] = ObjectSerializer::toQueryValue($r2lon, 'float');
        }
        // query params
        if ($r2dst !== null) {
            $queryParams['r2dst'] = ObjectSerializer::toQueryValue($r2dst, 'int64');
        }
        // query params
        if ($r2s !== null) {
            $queryParams['r2s'] = ObjectSerializer::toQueryValue($r2s, 'int64');
        }
        // query params
        if ($r2sc !== null) {
            $queryParams['r2sc'] = ObjectSerializer::toQueryValue($r2sc, 'int64');
        }
        // query params
        if ($r2bss !== null) {
            $queryParams['r2bss'] = ObjectSerializer::toQueryValue($r2bss, null);
        }
        // header params
        if ($accept_language !== null) {
            $headerParams['Accept-Language'] = ObjectSerializer::toHeaderValue($accept_language);
        }


        // body params
        $_tempBody = null;

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-listae-key');
        if ($apiKey !== null) {
            $headers['x-listae-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
