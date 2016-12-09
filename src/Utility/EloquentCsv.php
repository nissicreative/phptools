<?php
namespace Nissi\Utility;

use League\Csv\Writer;
use SplTempFileObject;
use Illuminate\Database\Eloquent\Collection;

class EloquentCsv
{
    protected $writer;
    protected $collection;
    protected $sample;
    protected $headings;

    public function __construct(Collection $collection, $headings = true)
    {
        $this->collection = $collection;
        $this->sample     = $collection->first();
        $this->headings   = $headings;

        $this->writer = Writer::createFromFileObject(new SplTempFileObject());
        $this->build();
    }

    /*
     * Build the CSV.
     */
    public function build()
    {
        $this->addHeadings();
        $this->insertRows();
    }

    /*
     * Create header row if applicable.
     */
    public function addHeadings()
    {
        if (empty($this->headings)) {
            return;
        }

        $keys = array_keys($this->sample->getAttributes());

        $headings = array_map(function ($val) {
            return str_humanize($val);
        }, $keys);

        $this->writer->insertOne($headings);
    }

    /*
     * Insert row for each record.
     */
    public function insertRows()
    {
        foreach ($this->collection as $record) {
            // May contain attributes that are not included in the collection
            $mutated = $record->toArray();

            // Filter out extraneous attributes
            $row = array_filter($mutated, function ($key) use ($record) {
                return array_key_exists($key, $record->getAttributes());
            }, ARRAY_FILTER_USE_KEY);

            $this->writer->insertOne($row);
        }
    }

    /*
     * Force-download CSV.
     */
    public function output($filename = 'export.csv')
    {
        $this->writer->output($filename);
    }

    /*
     * Return HTML string with class name.
     */
    public function toHtml($className = 'table')
    {
        return $this->writer->toHTML($className);
    }

    /*
     * Return string representation of CSV file.
     */
    public function __toString()
    {
        return $this->writer->__toString();
    }
}
