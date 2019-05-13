<?php
/**
 * Copyright (c) 2018.
 *
 * User: abdullah
 * Date: 26.08.2018
 * Time: 00:29
 */
namespace Basic\Collection;

use ArrayIterator;
use Closure;
use Traversable;
use ArrayAccess;

/**
 * Class Collection
 */
class Collection implements CollectionInterface
{
    
    /**
     * @var array Elements
     */
    protected $aElements = [];
    
    /**
     * Initializes a new ArrayCollection
     *
     * @param array $aElements
     * @param bool $bAsList
     */
    public function __construct(array $aElements = [], $bAsList = TRUE)
    {
        $this->aElements = $aElements;
    }
    
    /**
     * Creates a new instance from the specified elements.
     *
     * This method is provided for derived classes to specify how a new
     * instance should be created when constructor semantics have changed
     *
     * @param array $aElements Elements
     * @return static
     */
    protected function _createFromArray(array $aElements)
    {
        return new static($aElements);
    }
    
    /**
     * @param string $sJSON
     * @throws \InvalidArgumentException
     * @return array|null
     */
    public static function toArrayFromJson($sJSON)
    {
        if (!is_string($sJSON)) {
            throw new \InvalidArgumentException("The argument must be a valid JSON string");
        }
        $aOutput = json_decode($sJSON, TRUE);
        if ($aOutput === NULL) {
            $iLastJsonDecodeError = json_last_error();
            if ($iLastJsonDecodeError === JSON_ERROR_DEPTH) {
                throw new \InvalidArgumentException("The maximum stack depth has been exceeded (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_STATE_MISMATCH) {
                throw new \InvalidArgumentException("Invalid or malformed JSON (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_CTRL_CHAR) {
                throw new \InvalidArgumentException("Control character error, possibly incorrectly encoded (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_SYNTAX) {
                throw new \InvalidArgumentException("Syntax error (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_UTF8) {
                throw new \InvalidArgumentException("Malformed UTF-8 characters, possibly incorrectly encoded (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_RECURSION) {
                throw new \InvalidArgumentException("One or more recursive references in the value to be encoded (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_INF_OR_NAN) {
                throw new \InvalidArgumentException("One or more NAN or INF values in the value to be encoded (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_UNSUPPORTED_TYPE) {
                throw new \InvalidArgumentException("A value of a type that cannot be encoded was given (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_INVALID_PROPERTY_NAME) {
                throw new \InvalidArgumentException("A property name that cannot be encoded was given (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_UTF16) {
                throw new \InvalidArgumentException("Malformed UTF-16 characters, possibly incorrectly encoded (JSON Error)");
            }
            elseif ($iLastJsonDecodeError === JSON_ERROR_NONE) {
                return NULL;
            }
            else {
                throw new \InvalidArgumentException("Unknown JSON error");
            }
        }
        return $aOutput;
    }
    
    /**
     * @param string $sJSON
     * @param bool $bAsList
     * @return static
     */
    public function reconstructFromJson($sJSON, $bAsList = TRUE)
    {
        if ($bAsList) {
            $aElements = self::toArrayFromJson($sJSON);
            if (is_array($aElements)) {
                $this->aElements = $aElements;
            }
            else {
                $this->aElements = [];
            }
        }
        else {
            // todo:
        }
        return $this;
    }
    
    /**
     * Adds an element at the end of the collection
     *
     * @param mixed $xElement The element to add
     * @return static
     */
    public function add($xElement)
    {
        $this->aElements[] = $xElement;
        return $this;
    }
    
    /**
     * Clears the collection, removing all elements
     *
     * @return static
     */
    public function clear()
    {
        $this->aElements = [];
        return $this;
    }
    
    /**
     * Purify the collection
     *
     * @return static
     */
    public function purify()
    {
        //todo: array_filter
        return $this;
    }
    
    /**
     * Checks whether an element is contained in the collection.
     * This is an O(n) operation, where n is the size of the collection
     *
     * @param mixed $xElement The element to search for
     * @return bool TRUE if the collection contains the element, FALSE otherwise
     */
    public function contains($xElement)
    {
        return in_array($xElement, $this->aElements, TRUE);
    }
    
    /**
     * Checks whether the collection is empty (contains no elements)
     *
     * @return bool TRUE if the collection is empty, FALSE otherwise
     */
    public function isEmpty()
    {
        return empty($this->aElements);
    }
    
    /**
     * Checks whether the collection is loaded
     *
     * @return bool TRUE if the collection is loaded, FALSE otherwise
     */
    public function isLoaded()
    {
        return !empty($this->aElements);
    }
    
    /**
     * Removes the element at the specified index from the collection
     *
     * @param string|int $xKey The key/index of the element to remove
     * @return mixed The removed element or NULL, if the collection did not contain the element
     */
    public function remove($xKey)
    {
        if (! isset($this->aElements[$xKey]) && ! array_key_exists($xKey, $this->aElements)) {
            return NULL;
        }
        $xRemoved = $this->aElements[$xKey];
        unset($this->aElements[$xKey]);
        return $xRemoved;
    }
    
    /**
     * Removes the specified element from the collection, if it is found
     *
     * @param mixed $xElement The element to remove
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeElement($xElement)
    {
        $xKey = array_search($xElement, $this->aElements, TRUE);
        if ($xKey === FALSE) {
            return FALSE;
        }
        unset($this->aElements[$xKey]);
        return TRUE;
    }
    
    /**
     * Checks whether the collection contains an element with the specified key/index
     *
     * @param string|int $xKey The key/index to check for
     * @return bool TRUE if the collection contains an element with the specified key/index,
     *              FALSE otherwise
     */
    public function containsKey($xKey)
    {
        return isset($this->aElements[$xKey]) || array_key_exists($xKey, $this->aElements);
    }
    
    /**
     * Gets the element at the specified key/index
     *
     * @param string|int $xKey The key/index of the element to retrieve
     * @return mixed
     */
    public function get($xKey)
    {
        return $this->elements[$xKey] ?? NULL;
    }
    
    /**
     * Gets all keys/indices of the collection
     *
     * @return array The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection
     */
    public function getKeyArray()
    {
        return array_keys($this->aElements);
    }
    
    /**
     * Gets all values of the collection
     *
     * @return array The values of all elements in the collection, in the order they
     *               appear in the collection
     */
    public function getValueArray()
    {
        return array_values($this->aElements);
    }
    
    /**
     * Sets an element in the collection at the specified key/index
     *
     * @param string|int $xKey The key/index of the element to set
     * @param mixed $xElement The element to set
     * @return static
     */
    public function set($xKey, $xElement)
    {
        $this->aElements[$xKey] = $xElement;
        return $this;
    }
    
    /**
     * Gets a native PHP array representation of the collection
     *
     * @return array
     */
    public function toArray()
    {
        return $this->aElements;
    }
    
    /**
     * Sets the internal iterator to the first element in the collection and returns this element
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->aElements);
    }
    
    /**
     * Sets the internal iterator to the last element in the collection and returns this element
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->aElements);
    }
    
    /**
     * Gets the key/index of the element at the current iterator position
     *
     * @return int|string
     */
    public function key()
    {
        return key($this->aElements);
    }
    
    /**
     * Gets the element of the collection at the current iterator position
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->aElements);
    }
    
    /**
     * Moves the internal iterator position to the next element and returns this element
     *
     * @return mixed
     */
    public function next()
    {
        return next($this->aElements);
    }
    
    /**
     * Tests for the existence of an element that satisfies the given predicate
     *
     * @param Closure $cPredicate The predicate
     * @return bool TRUE if the predicate is TRUE for at least one element, FALSE otherwise
     */
    public function exists(Closure $cPredicate)
    {
        foreach ($this->aElements as $xKey => $xElement) {
            if ($cPredicate($xKey, $xElement)) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * Returns all the elements of this collection that satisfy the predicate p.
     * The order of the elements is preserved
     *
     * @param Closure $cPredicate The predicate used for filtering
     * @return static A collection with the results of the filter operation
     */
    public function filter(Closure $cPredicate)
    {
        return $this->_createFromArray(array_filter($this->aElements, $cPredicate));
    }
    
    /**
     * Tests whether the given predicate holds for all elements of this collection
     *
     * @param Closure $cPredicate The predicate
     * @return bool TRUE, if the predicate yields TRUE for all elements, FALSE otherwise
     */
    public function forAll(Closure $cPredicate)
    {
        foreach ($this->aElements as $xKey => $xElement) {
            if (!$cPredicate($xKey, $xElement)) {
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function
     *
     * @param Callable $cFunction The function
     * @return static
     */
    public function map(callable $cFunction)
    {
        return $this->_createFromArray(array_map($cFunction, $this->aElements));
    }
    
    /**
     * @param object $oObject
     * @return object
     */
    protected static function getNewInstanceOf($oObject)
    {
        if (!is_object($oObject)) {
            throw new \InvalidArgumentException("First argument must be an object");
        }
        $sClass = get_class($oObject);
        return new $sClass();
    }
    
    /**
     * @param object|array $xI
     * @param Callable $cFunction($xValue)
     * @param bool $bForceCollection
     * @param object|array|null $pxExport
     * @throws \InvalidArgumentException
     * @return object|array
     */
    public static function mapIterableInDepth($xI, callable $cFunction, $bForceCollection = FALSE, &$pxExport = NULL)
    {
        if (is_array($xI)) {
            $xO = $bForceCollection ? new static() : [];
        }
        elseif (is_object($xI) && $xI instanceof Traversable) {
            $xO = $bForceCollection ? new static() : (($xI instanceof ArrayAccess) ? self::getNewInstanceOf($xI) : new static());
        }
        else {
            throw new \InvalidArgumentException("First argument must be iterable");
        }
        foreach ($xI as $xKey => &$pxValue) {
            if (is_array($pxValue) || (is_object($pxValue) && $pxValue instanceof Traversable)) {
                if (is_array($pxExport) || (is_object($pxExport) && $pxExport instanceof ArrayAccess)) {
                    $xO[$xKey] = self::mapIterableInDepth($pxValue, $cFunction, $bForceCollection, $pxExport);
                }
                else {
                    $xO[$xKey] = self::mapIterableInDepth($pxValue, $cFunction, $bForceCollection);
                }
            }
            else {
                if (is_array($pxExport) || (is_object($pxExport) && $pxExport instanceof ArrayAccess)) {
                    $xO[$xKey] = $pxExport[$xKey] = call_user_func($cFunction, $pxValue);
                }
                else {
                    $xO[$xKey] = call_user_func($cFunction, $pxValue);
                }
            }
        }
        return $xO;
    }
    
    /**
     * @param callable $cFunction
     * @param bool $bForceCollection
     * @param CollectionInterface|null $oExport
     * @return CollectionInterface
     */
    public function mapInDepth(callable $cFunction, $bForceCollection = FALSE, CollectionInterface $oExport = NULL)
    {
        if (is_object($oExport) && $oExport instanceof CollectionInterface) {
            self::mapIterableInDepth($this->aElements, $cFunction, $bForceCollection, $oExport);
            return $this;
        }
        return self::mapIterableInDepth($this->aElements, $cFunction, TRUE);
    }
    
    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections
     *
     * @param Closure $cPredicate The predicate on which to partition
     * @return static[] An array with two elements. The first element contains the collection
     *                               of elements where the predicate returned FALSE, the second element
     *                               contains the collection of elements where the predicate returned TRUE
     */
    public function partition(Closure $cPredicate)
    {
        $aMatches = $aNotMatches = [];
        foreach ($this->aElements as $xKey => $xElement) {
            if ($cPredicate($xKey, $xElement)) {
                $aMatches[$xKey] = $xElement;
            }
            else {
                $aNotMatches[$xKey] = $xElement;
            }
        }
        return [ $this->_createFromArray($aNotMatches), $this->_createFromArray($aMatches) ];
    }
    
    /**
     * Gets the index/key of a given element. The comparison of two elements is strict,
     * that means not only the value but also the type must match.
     * For objects this means reference equality
     *
     * @param mixed $xElement The element to search for
     * @return int|string|bool The key/index of the element or FALSE if the element was not found
     */
    public function indexOf($xElement)
    {
        return array_search($xElement, $this->aElements, TRUE);
    }
    
    /**
     * Extracts a slice of $length elements starting at position $offset from the Collection.
     *
     * If $length is null it returns all elements from $offset to the end of the Collection.
     * Keys have to be preserved by this method. Calling this method will only return the
     * selected slice and NOT change the elements contained in the collection slice is called on
     *
     * @param int $iOffset The offset to start from
     * @param int|null $iLength The maximum number of elements to return, or null for no limit
     *
     * @return array
     */
    public function slice($iOffset, $iLength = NULL)
    {
        return array_slice($this->aElements, $iOffset, $iLength, TRUE);
    }
    
    /**
     * Retrieve an external iterator
     *
     * @return Traversable An instance of an object implementing Iterator or Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->aElements);
    }
    
    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for
     * @return boolean true on success or false on failure.
     *                 The return value will be casted to boolean if non-boolean was returned
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }
    
    /**
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve
     * @return mixed Can return all value types
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            $this->add($value);
            return;
        }
        $this->set($offset, $value);
    }
    
    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
    
    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer. The return value is cast to an integer
     */
    public function count()
    {
        return count($this->aElements);
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '@' . spl_object_hash($this);
    }
    
    /**
     * @param array $aElements
     * @return bool
     */
    protected static function _isArraySequential($aElements)
    {
        $iTotalNumberOfElements = count($aElements);
        if ($iTotalNumberOfElements < 1) {
            return FALSE;
        }
        return count(array_filter(array_keys($aElements), 'is_int')) === $iTotalNumberOfElements;
    }
    
    /**
     * @param object|array $xElements
     * @throws \InvalidArgumentException
     * @return bool
     */
    public static function isSequential($xElements)
    {
        if (is_object($xElements) && method_exists($xElements, 'toArray')) {
            return self::_isArraySequential($xElements->toArray());
        }
        elseif (is_array($xElements)) {
            return self::_isArraySequential($xElements);
        }
        throw new \InvalidArgumentException("The argument must be an iterative object or an array");
    }
    
    /**
     * @return bool
     */
    public function sequential()
    {
        return self::_isArraySequential($this->aElements);
    }
    
    /**
     * @param array $aElements
     * @return bool
     */
    protected static function _isArrayAssociative($aElements)
    {
        $iTotalNumberOfElements = count($aElements);
        if ($iTotalNumberOfElements < 1) {
            return FALSE;
        }
        return count(array_filter(array_keys($aElements), 'is_int')) < $iTotalNumberOfElements;
    }
    
    /**
     * @param object|array $xElements
     * @throws \InvalidArgumentException
     * @return bool
     */
    public static function isAssociative($xElements)
    {
        if (is_object($xElements) && method_exists($xElements, 'toArray')) {
            return self::_isArrayAssociative($xElements->toArray());
        }
        elseif (is_array($xElements)) {
            return self::_isArrayAssociative($xElements);
        }
        throw new \InvalidArgumentException("The argument must be an iterative object or an array");
    }
    
    /**
     * @return bool
     */
    public function associative()
    {
        return self::_isArrayAssociative($this->aElements);
    }
    
    /**
     * @param array $aElements
     * @param mixed $pxValue
     * @param mixed $pxKey
     * @return void
     */
    protected static function _fillValueAndKeyByFirstElementOfArray($aElements, &$pxValue = NULL, &$pxKey = NULL)
    {
        if (empty($aElements)) {
            $pxValue = NULL;
            $pxKey = NULL;
        }
        else {
            $pxValue = reset($aElements);
            $pxKey = key($aElements);
        }
    }
    
    /**
     * @param object|array $xElements
     * @param mixed $pxValue
     * @param mixed $pxKey
     * @return void
     */
    public static function fillValueAndKeyByFirstElementOfList($xElements, &$pxValue = NULL, &$pxKey = NULL)
    {
        if (is_object($xElements) && method_exists($xElements, 'toArray')) {
            self::_fillValueAndKeyByFirstElementOfArray($xElements->toArray(), $pxValue, $pxKey);
            return;
        }
        elseif (is_array($xElements)) {
            self::_fillValueAndKeyByFirstElementOfArray($xElements, $pxValue, $pxKey);
            return;
        }
        throw new \InvalidArgumentException("The argument must be an iterative object or an array");
    }
    
    /**
     * @param mixed $pxValue
     * @param mixed $pxKey
     * @return static
     */
    public function fillValueAndKeyByFirstElement(&$pxValue = NULL, &$pxKey = NULL)
    {
        self::_fillValueAndKeyByFirstElementOfArray($this->aElements, $pxValue, $pxKey);
        return $this;
    }
    
    /**
     * @param array $aElements
     * @param mixed $pxValue
     * @param mixed $pxKey
     * @return void
     */
    protected static function _fillValueAndKeyByLastElementOfArray($aElements, &$pxValue = NULL, &$pxKey = NULL)
    {
        if (empty($aElements)) {
            $pxValue = NULL;
            $pxKey = NULL;
        }
        else {
            $pxValue = end($aElements);
            $pxKey = key($aElements);
        }
    }
    
    /**
     * @param object|array $xElements
     * @param mixed $pxValue
     * @param mixed $pxKey
     * @return void
     */
    public static function fillValueAndKeyByLastElementOfList($xElements, &$pxValue = NULL, &$pxKey = NULL)
    {
        if (is_object($xElements) && method_exists($xElements, 'toArray')) {
            self::_fillValueAndKeyByLastElementOfArray($xElements->toArray(), $pxValue, $pxKey);
            return;
        }
        elseif (is_array($xElements)) {
            self::_fillValueAndKeyByLastElementOfArray($xElements, $pxValue, $pxKey);
            return;
        }
        throw new \InvalidArgumentException("The argument must be an iterative object or an array");
    }
    
    /**
     * @param mixed $pxValue
     * @param mixed $pxKey
     * @return static
     */
    public function fillValueAndKeyByLastElement(&$pxValue = NULL, &$pxKey = NULL)
    {
        self::_fillValueAndKeyByLastElementOfArray($this->aElements, $pxValue, $pxKey);
        return $this;
    }
    
    /**
     * @param int $iSortFlags (SORT_REGULAR | SORT_NUMERIC | SORT_STRING | SORT_LOCALE_STRING)
     * @return static
     */
    public function uniquify($iSortFlags = SORT_REGULAR)
    {
        if (empty($this->aElements)) {
            return $this;
        }
        $this->aElements = array_unique($this->aElements, $iSortFlags);
        return $this;
    }
    
    public function unshift()
    {
        $iNumberOfArguments = func_num_args();
        if ($iNumberOfArguments < 2) {
            return count($this->aElements);
        }
        $aArguments = func_get_args();
        $iNumberOfElementsInArray = 0;
        for ($i = 1; $i < $iNumberOfArguments; $i++) {
            // todo:
        }
    }
    
    /**
     * Flatten a multi-dimensional array into a one dimensional array
     *
     * @param array $aI The array to flatten
     * @param bool $bPreserveKeys Whether or not to preserve array keys. Keys from deeply nested arrays will
     *                            overwrite keys from shallowy nested arrays
     * @param bool $bKeepInitials To keep initial elements
     * @return array
     */
    public static function flattenArray($aI, $bPreserveKeys = TRUE, $bKeepInitials = FALSE)
    {
        $aFlattened = [];
        array_walk_recursive($aI, function ($xValue, $xKey) use (&$aFlattened, $bPreserveKeys, $bKeepInitials) {
            if ($bPreserveKeys && !is_int($xKey)) {
                if ($bKeepInitials) {
                    if (!array_key_exists($xKey, $aFlattened)) {
                        $aFlattened[$xKey] = $xValue;
                    }
                }
                else {
                    $aFlattened[$xKey] = $xValue;
                }
            }
            else {
                $aFlattened[] = $xValue;
            }
        });
        return $aFlattened;
    }
    
    public static function arrayify()
    {
        // todo:
    }
    
    /**
     * @param array $a
     * @param string $sFix
     * @return mixed
     */
    public static function prependEachKey($a, $sFix)
    {
        if () { // todo:
            $aO = [];
            foreach ($a as $xKey => $xValue) {
                $aO[$sFix . $xKey] = $xValue;
            }
            return $aO;
        }
        return $a;
    }
    
    /**
     * @param array $a
     * @param string $sFix
     * @return mixed
     */
    public static function appendEachKey($a, $sFix)
    {
        if () { // todo:
            $aO = [];
            foreach ($a as $xKey => $xValue) {
                $aO[$xKey . $sFix] = $xValue;
            }
            return $aO;
        }
        return $a;
    }
    
    /**
     * @param array $a
     * @param $sVariableName
     * @return null|string
     */
    protected static function getConstructionPhpCode($a, $sVariableName)
    {
        if () { // todo:
            $sO = '';
            $sVariable = '$' . $sVariableName;
            foreach ($a as $xKey => $xValue) {
                if (is_string($xKey)) {
                    $xKey = "'" . $xKey . "'";
                }
                if (is_array($xValue)) {
                    $sStack = self::getConstructionPhpCode($xValue, ltrim($sVariable, "$") . '[' . $xKey . ']');
                    if ($sStack === NULL) {
                        return NULL;
                    }
                    $sO .= $sStack;
                }
                elseif (is_scalar($xValue)) {
                    if (is_string($xValue)) {
                        $xValue = "'" . str_replace("'", "\\'", $xValue) . "'";
                    }
                    $sO .= $sVariable . '[' . $xKey . '] = ' . $xValue . ';' . PHP_EOL;
                }
                else {
                    return NULL;
                }
            }
            return $sO;
        }
        return NULL;
    }
    
    protected static function groupBy()
    {
        // todo:
    }
    
    protected static function orderBy()
    {
        // todo:
    }
    
    protected static function uniqueBy()
    {
        // todo:
    }
    
    protected static function filterBy()
    {
        // todo:
    }
    
    protected static function mergeBy()
    {
        // todo:
    }
    
}