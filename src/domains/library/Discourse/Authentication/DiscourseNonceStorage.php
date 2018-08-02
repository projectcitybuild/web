<?php
namespace Domains\Library\Discourse\Authentication;

use Illuminate\Http\Request;
use Illuminate\Session\Store;


class DiscourseNonceStorage 
{

    private const SESSION_KEY = 'discourse_sso';

    /**
     * @var Request
     */
    private $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function getSession() : Store
    {
        return $this->request->session();
    }

    public function store(string $nonce, string $returnUri) 
    {
        $this->getSession()->put([
            self::SESSION_KEY => [
                'nonce'      => $nonce,
                'return_uri' => $returnUri,
            ],
        ]);
    }

    public function get() : array
    {
        return $this->getSession()->get(self::SESSION_KEY);
    }

    public function clear()
    {
        $this->getSession()->forget(self::SESSION_KEY);
    }

}