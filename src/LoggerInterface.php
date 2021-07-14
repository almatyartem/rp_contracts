<?

namespace GuzzleWrapper;

interface LoggerInterface
{
    /**
     * @param ResultWrapper $result
     * @return mixed
     */
    public function log(ResultWrapper $result);
}
