<?php


namespace EMA\Domain\Foundation\Exception;

/**
 * This trait will add message translation to any exception.
 * The goal of this is to support every exception with string problem code and possible message
 */
trait KnownProblem
{
    /** @var  string code to look up in documentation like "112_EMAIL_INVALID" */
    protected $problem_code;
    /** @var  array of additional data (for logging/debugging purposes) */
    protected $payload;
    
    /**
     * Will make a new exception with required data filled in
     *
     * @param string $problem_code
     * @param string $message
     * @param array  $payload
     * @param \Exception|null $prev
     *
     * @return static
     */
    static public function withProblem(
        string $problem_code,
        string $message = "",
        array $payload = [],
        \Exception $prev = null
    ): self {
        $exception = new static($message, 0, $prev);
        
        // TODO can I do this without public setters?
        $exception->setProblemCode($problem_code);
        $exception->setMessagePayload($payload);
        
        return $exception;
    }
    
    /**
     * @param string $problem_code
     */
    public function setProblemCode(string $problem_code): string
    {
        $this->problem_code = $problem_code;
    }
    
    
    /**
     * @param array $message_payload
     */
    public function setMessagePayload(array $message_payload): array
    {
        $this->message_payload = $message_payload;
    }
    
    /**
     * @return array
     */
    public function getMessagePayload(): array
    {
        return $this->payload;
    }
    
    /**
     * @return string
     */
    public function getProblemCode(): string
    {
        return $this->problem_code;
    }
    
    
}