<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Install\Validator;

use MongoDB\Client;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

/**
 * Validates a mongo db connection.
 *
 * Tries to connect using a connection string.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 * @todo   write test when a test database is available
 */
class MongoDbConnection extends AbstractValidator
{
    const NO_CONNECTION = 'connectionFails';

    /**
     * Options
     *
     * @var array
     */
    protected $options = array(
        'translatorTextDomain' => 'Install',
    );

    /**
     * Message templates.
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NO_CONNECTION => /* @translate */ 'Connecting the database failed: %databaseError%',
    );

    /**
     * Message variables
     *
     * @var array
     */
    protected $messageVariables = array(
        'databaseError' => 'databaseError'
    );

    /**
     * Last database error message, if one.
     *
     * @var string|null
     */
    protected $databaseError;

    /**
     * Returns true if and only if a mongodb connection can be established.
     *
     * @param  string $value The mongodb connection string
     *
     * @return bool
     */
    public function isValid($value)
    {
        $this->databaseError = null;

        // @codeCoverageIgnoreStart
        // This cannot be testes until we have a test database environment.
        try {
            $connection = new Client($value);
            $connection->listDatabases();
        } catch (ConnectionTimeoutException $e) {
            $this->databaseError = $e->getMessage();
            $this->error(self::NO_CONNECTION);
            return false;
        } catch (\Exception $e) {
            $this->databaseError = $e->getMessage();
            $this->error(self::NO_CONNECTION);
            return false;
        }

        return true;
        // @codeCoverageIgnoreEnd
    }
}
