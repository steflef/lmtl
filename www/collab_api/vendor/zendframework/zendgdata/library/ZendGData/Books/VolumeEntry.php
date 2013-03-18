<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   ZendGData
 */

namespace ZendGData\Books;

use ZendGData\Books;

/**
 * Describes an entry in a feed of Book Search volumes
 *
 * @category   Zend
 * @package    ZendGData
 * @subpackage Books
 */
class VolumeEntry extends \ZendGData\Entry
{

    const THUMBNAIL_LINK_REL = 'http://schemas.google.com/books/2008/thumbnail';
    const PREVIEW_LINK_REL = 'http://schemas.google.com/books/2008/preview';
    const INFO_LINK_REL = 'http://schemas.google.com/books/2008/info';
    const ANNOTATION_LINK_REL = 'http://schemas.google.com/books/2008/annotation';

    protected $_comments = null;
    protected $_creators = array();
    protected $_dates = array();
    protected $_descriptions = array();
    protected $_embeddability = null;
    protected $_formats = array();
    protected $_identifiers = array();
    protected $_languages = array();
    protected $_publishers = array();
    protected $_rating = null;
    protected $_review = null;
    protected $_subjects = array();
    protected $_titles = array();
    protected $_viewability = null;

    /**
     * Constructor for ZendGData\Books\VolumeEntry which
     * Describes an entry in a feed of Book Search volumes
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Books::$namespaces);
        parent::__construct($element);
    }

    /**
     * Retrieves DOMElement which corresponds to this element and all
     * child properties. This is used to build this object back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistance.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_creators !== null) {
            foreach ($this->_creators as $creators) {
                $element->appendChild($creators->getDOM(
                    $element->ownerDocument));
            }
        }
        if ($this->_dates !== null) {
            foreach ($this->_dates as $dates) {
                $element->appendChild($dates->getDOM($element->ownerDocument));
            }
        }
        if ($this->_descriptions !== null) {
            foreach ($this->_descriptions as $descriptions) {
                $element->appendChild($descriptions->getDOM(
                    $element->ownerDocument));
            }
        }
        if ($this->_formats !== null) {
            foreach ($this->_formats as $formats) {
                $element->appendChild($formats->getDOM(
                    $element->ownerDocument));
            }
        }
        if ($this->_identifiers !== null) {
            foreach ($this->_identifiers as $identifiers) {
                $element->appendChild($identifiers->getDOM(
                    $element->ownerDocument));
            }
        }
        if ($this->_languages !== null) {
            foreach ($this->_languages as $languages) {
                $element->appendChild($languages->getDOM(
                    $element->ownerDocument));
            }
        }
        if ($this->_publishers !== null) {
            foreach ($this->_publishers as $publishers) {
                $element->appendChild($publishers->getDOM(
                    $element->ownerDocument));
            }
        }
        if ($this->_subjects !== null) {
            foreach ($this->_subjects as $subjects) {
                $element->appendChild($subjects->getDOM(
                    $element->ownerDocument));
            }
        }
        if ($this->_titles !== null) {
            foreach ($this->_titles as $titles) {
                $element->appendChild($titles->getDOM($element->ownerDocument));
            }
        }
        if ($this->_comments !== null) {
            $element->appendChild($this->_comments->getDOM(
                $element->ownerDocument));
        }
        if ($this->_embeddability !== null) {
            $element->appendChild($this->_embeddability->getDOM(
                $element->ownerDocument));
        }
        if ($this->_rating !== null) {
            $element->appendChild($this->_rating->getDOM(
                $element->ownerDocument));
        }
        if ($this->_review !== null) {
            $element->appendChild($this->_review->getDOM(
                $element->ownerDocument));
        }
        if ($this->_viewability !== null) {
            $element->appendChild($this->_viewability->getDOM(
                $element->ownerDocument));
        }
        return $element;
    }

    /**
     * Creates individual objects of the appropriate type and stores
     * them in this object based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process.
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('dc') . ':' . 'creator':
            $creators = new \ZendGData\DublinCore\Extension\Creator();
            $creators->transferFromDOM($child);
            $this->_creators[] = $creators;
            break;
        case $this->lookupNamespace('dc') . ':' . 'date':
            $dates = new \ZendGData\DublinCore\Extension\Date();
            $dates->transferFromDOM($child);
            $this->_dates[] = $dates;
            break;
        case $this->lookupNamespace('dc') . ':' . 'description':
            $descriptions = new \ZendGData\DublinCore\Extension\Description();
            $descriptions->transferFromDOM($child);
            $this->_descriptions[] = $descriptions;
            break;
        case $this->lookupNamespace('dc') . ':' . 'format':
            $formats = new \ZendGData\DublinCore\Extension\Format();
            $formats->transferFromDOM($child);
            $this->_formats[] = $formats;
            break;
        case $this->lookupNamespace('dc') . ':' . 'identifier':
            $identifiers = new \ZendGData\DublinCore\Extension\Identifier();
            $identifiers->transferFromDOM($child);
            $this->_identifiers[] = $identifiers;
            break;
        case $this->lookupNamespace('dc') . ':' . 'language':
            $languages = new \ZendGData\DublinCore\Extension\Language();
            $languages->transferFromDOM($child);
            $this->_languages[] = $languages;
            break;
        case $this->lookupNamespace('dc') . ':' . 'publisher':
            $publishers = new \ZendGData\DublinCore\Extension\Publisher();
            $publishers->transferFromDOM($child);
            $this->_publishers[] = $publishers;
            break;
        case $this->lookupNamespace('dc') . ':' . 'subject':
            $subjects = new \ZendGData\DublinCore\Extension\Subject();
            $subjects->transferFromDOM($child);
            $this->_subjects[] = $subjects;
            break;
        case $this->lookupNamespace('dc') . ':' . 'title':
            $titles = new \ZendGData\DublinCore\Extension\Title();
            $titles->transferFromDOM($child);
            $this->_titles[] = $titles;
            break;
        case $this->lookupNamespace('gd') . ':' . 'comments':
            $comments = new \ZendGData\Extension\Comments();
            $comments->transferFromDOM($child);
            $this->_comments = $comments;
            break;
        case $this->lookupNamespace('gbs') . ':' . 'embeddability':
            $embeddability = new Extension\Embeddability();
            $embeddability->transferFromDOM($child);
            $this->_embeddability = $embeddability;
            break;
        case $this->lookupNamespace('gd') . ':' . 'rating':
            $rating = new \ZendGData\Extension\Rating();
            $rating->transferFromDOM($child);
            $this->_rating = $rating;
            break;
        case $this->lookupNamespace('gbs') . ':' . 'review':
            $review = new Extension\Review();
            $review->transferFromDOM($child);
            $this->_review = $review;
            break;
        case $this->lookupNamespace('gbs') . ':' . 'viewability':
            $viewability = new Extension\Viewability();
            $viewability->transferFromDOM($child);
            $this->_viewability = $viewability;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Returns the Comments class
     *
     * @return \ZendGData\Extension\Comments|null The comments
     */
    public function getComments()
    {
        return $this->_comments;
    }

    /**
     * Returns the creators
     *
     * @return array The creators
     */
    public function getCreators()
    {
        return $this->_creators;
    }

    /**
     * Returns the dates
     *
     * @return array The dates
     */
    public function getDates()
    {
        return $this->_dates;
    }

    /**
     * Returns the descriptions
     *
     * @return array The descriptions
     */
    public function getDescriptions()
    {
        return $this->_descriptions;
    }

    /**
     * Returns the embeddability
     *
     * @return \ZendGData\Books\Extension\Embeddability|null The embeddability
     */
    public function getEmbeddability()
    {
        return $this->_embeddability;
    }

    /**
     * Returns the formats
     *
     * @return array The formats
     */
    public function getFormats()
    {
        return $this->_formats;
    }

    /**
     * Returns the identifiers
     *
     * @return array The identifiers
     */
    public function getIdentifiers()
    {
        return $this->_identifiers;
    }

    /**
     * Returns the languages
     *
     * @return array The languages
     */
    public function getLanguages()
    {
        return $this->_languages;
    }

    /**
     * Returns the publishers
     *
     * @return array The publishers
     */
    public function getPublishers()
    {
        return $this->_publishers;
    }

    /**
     * Returns the rating
     *
     * @return \ZendGData\Extension\Rating|null The rating
     */
    public function getRating()
    {
        return $this->_rating;
    }

    /**
     * Returns the review
     *
     * @return \ZendGData\Books\Extension\Review|null The review
     */
    public function getReview()
    {
        return $this->_review;
    }

    /**
     * Returns the subjects
     *
     * @return array The subjects
     */
    public function getSubjects()
    {
        return $this->_subjects;
    }

    /**
     * Returns the titles
     *
     * @return array The titles
     */
    public function getTitles()
    {
        return $this->_titles;
    }

    /**
     * Returns the viewability
     *
     * @return \ZendGData\Books\Extension\Viewability|null The viewability
     */
    public function getViewability()
    {
        return $this->_viewability;
    }

    /**
     * Sets the Comments class
     *
     * @param \ZendGData\Extension\Comments|null $comments Comments class
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setComments($comments)
    {
        $this->_comments = $comments;
        return $this;
    }

    /**
     * Sets the creators
     *
     * @param array $creators Creators|null
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setCreators($creators)
    {
        $this->_creators = $creators;
        return $this;
    }

    /**
     * Sets the dates
     *
     * @param array $dates dates
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setDates($dates)
    {
        $this->_dates = $dates;
        return $this;
    }

    /**
     * Sets the descriptions
     *
     * @param array $descriptions descriptions
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setDescriptions($descriptions)
    {
        $this->_descriptions = $descriptions;
        return $this;
    }

    /**
     * Sets the embeddability
     *
     * @param \ZendGData\Books\Extension\Embeddability|null $embeddability
     *        embeddability
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setEmbeddability($embeddability)
    {
        $this->_embeddability = $embeddability;
        return $this;
    }

    /**
     * Sets the formats
     *
     * @param array $formats formats
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setFormats($formats)
    {
        $this->_formats = $formats;
        return $this;
    }

    /**
     * Sets the identifiers
     *
     * @param array $identifiers identifiers
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setIdentifiers($identifiers)
    {
        $this->_identifiers = $identifiers;
        return $this;
    }

    /**
     * Sets the languages
     *
     * @param array $languages languages
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setLanguages($languages)
    {
        $this->_languages = $languages;
        return $this;
    }

    /**
     * Sets the publishers
     *
     * @param array $publishers publishers
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setPublishers($publishers)
    {
        $this->_publishers = $publishers;
        return $this;
    }

    /**
     * Sets the rating
     *
     * @param \ZendGData\Extension\Rating|null $rating rating
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setRating($rating)
    {
        $this->_rating = $rating;
        return $this;
    }

    /**
     * Sets the review
     *
     * @param \ZendGData\Books\Extension\Review|null $review review
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setReview($review)
    {
        $this->_review = $review;
        return $this;
    }

    /**
     * Sets the subjects
     *
     * @param array $subjects subjects
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setSubjects($subjects)
    {
        $this->_subjects = $subjects;
        return $this;
    }

    /**
     * Sets the titles
     *
     * @param array $titles titles
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setTitles($titles)
    {
        $this->_titles = $titles;
        return $this;
    }

    /**
     * Sets the viewability
     *
     * @param \ZendGData\Books\Extension\Viewability|null $viewability
     *        viewability
     * @return \ZendGData\Books\VolumeEntry Provides a fluent interface
     */
    public function setViewability($viewability)
    {
        $this->_viewability = $viewability;
        return $this;
    }


    /**
     * Gets the volume ID based upon the atom:id value
     *
     * @return string The volume ID
     * @throws \ZendGData\App\Exception
     */
    public function getVolumeId()
    {
        $fullId = $this->getId()->getText();
        $position = strrpos($fullId, '/');
        if ($position === false) {
            throw new \ZendGData\App\Exception('Slash not found in atom:id');
        } else {
            return substr($fullId, strrpos($fullId,'/') + 1);
        }
    }

    /**
     * Gets the thumbnail link
     *
     * @return \ZendGData\App\Extension\link|null The thumbnail link
     */
    public function getThumbnailLink()
    {
        return $this->getLink(self::THUMBNAIL_LINK_REL);
    }

    /**
     * Gets the preview link
     *
     * @return \ZendGData\App\Extension\Link|null The preview link
     */
    public function getPreviewLink()
    {
        return $this->getLink(self::PREVIEW_LINK_REL);
    }

    /**
     * Gets the info link
     *
     * @return \ZendGData\App\Extension\Link|null The info link
     */
    public function getInfoLink()
    {
        return $this->getLink(self::INFO_LINK_REL);
    }

    /**
     * Gets the annotations link
     *
     * @return \ZendGData\App\Extension\Link|null The annotations link
     */
    public function getAnnotationLink()
    {
        return $this->getLink(self::ANNOTATION_LINK_REL);
    }

}
