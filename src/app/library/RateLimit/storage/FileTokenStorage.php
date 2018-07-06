<?php
namespace App\Library\RateLimit\Storage;

use App\Library\RateLimit\TokenStorable;
use App\Library\RateLimit\TokenState;

/**
 * For use with global-scoped rate limiting
 */
class FileTokenStorage implements TokenStorable {

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var int
     */
    private $initialTokens;


    public function __construct(string $filePath, int $initialTokens = 0) {
        $this->filePath = $filePath;
        $this->initialTokens = $initialTokens;
    }

    public function bootstrap() {}

    /**
     * Obtains an exclusive file lock for writing,
     * or a shared (read-only) lock for reading
     *
     * @param boolean $forWriting
     * @return void
     */
    private function obtainLock(bool $forWriting = false) {
        return flock($file, $forWriting ? LOCK_EX : LOCK_SH);
    }

    /**
     * Unlocks the given file to allow other
     * processes access to it
     *
     * @param Resource $resource
     * @return void
     */
    private function unlock($resource) {
        return flock($resource, LOCK_UN);
    }

    /**
     * Runs a closure on a resource after obtaining
     * a file lock. The file is automatically unlocked
     * and closed regardless of failure.
     *
     * @param boolean $forWriting
     * @param Closure $block
     * @return TokenState|null
     */
    private function withLock(bool $forWriting, Closure $block) :? TokenState {
        $file = fopen($this->filePath, $forWriting ? 'w+' : 'r+');

        $result = null;
        if ($this->obtainLock($forWriting)) {
            try {
                $result = $block($file);

            } finally {
                fclose($file);
                $this->unlock($file);
            }
            return $result;

        } else {
            throw new \Exception('Failed to obtain file lock for token storage');
        }
    }

    /**
     * Returns whether the file already exists or not
     *
     * @return boolean
     */
    private function fileExists() : bool {
        return file_exists($this->filePath);
    }

    /**
     * Reads from file (with a shared read-access lock)
     *
     * @return TokenState
     */
    public function deserialize() : TokenState {
        if (!fileExists()) {
            touch($this->filePath);
            return new TokenState($this->initialTokens, 0);
        }

        return $this->withLock(false, function($file) {
            $json = stream_get_contents($file);
            return TokenState::fromJSON($json);
        });
    }

    /**
     * Writes to file (with an exclusive lock)
     *
     * @param TokenState $data
     * @return void
     */
    public function serialize(TokenState $data) {
        $this->withLock(true, function($file) use($data) {
            fwrite($file, $data->toJSON());
        });
    }

}