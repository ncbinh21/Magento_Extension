<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 */

namespace Forix\Custom\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadImage extends Command
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \FishPig\WordPress\Model\ResourceModel\Post\Collection
     */
    protected $postCollection;

    public function __construct(
        \Magento\Framework\App\State $state,
        $name = null
    )
    {
        try {
            $state->setAreaCode('frontend');
        } catch (\Exception $e) {
            $state->setAreaCode('frontend');

        }
        parent::__construct($name);
    }

    /**
     *
     */
    public function configure()
    {
        $this->setName('forix:downloadimage');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start!!!");
        $this->downloadImageBlog();
        $output->writeln("End!!!");
    }

    /**
     * @return Void
     */
    public function downloadImageBlog()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $postCollection = $objectManager->create('FishPig\WordPress\Model\ResourceModel\Post\Collection');
        foreach ($postCollection as $post) {
            if ($post->getPostContent()) {
                $data['content'] = $post->getPostContent();
                preg_match_all('/src="([^"]+)/i', $data['content'], $images);
                if (isset($images[1][0]) && strpos($images[1][0], 'www.softstarshoes.com/blog/wp-content/uploads')) {
                    if ($post->getImage()) {
                        try {
                            $homepage = file_get_contents($images[1][0]);
                            echo 'Post Id: ' . $post->getId() . "\n";
                            echo 'Generate image with url: ' . $images[1][0] . "\n";
                            $nameImage = explode('/', $post->getImage()->getFile());
                            $folder = 'sssblog/wp-content/uploads/forix/' . $nameImage[0] . '/' . $nameImage[1];
                            if (!file_exists($folder)) {
                                mkdir($folder, 0777, true);
                            }
                            $filename = $folder . '/' . $nameImage[2];
                            $handle = fopen($filename, "w");
                            fwrite($handle, $homepage);
                            fclose($handle);
                            $this->resizeImage('http://softstarshoes.mage.forixdemo.com/' . $filename);
                            echo 'Generate Success !!!' . "\n";
                        } catch (\Exception $exception) {
                            die(var_dump($exception->getMessage()));
                        }
                    }
                }
            }
        }
        return $this;
    }

    protected function resizeImage($filename)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $reizeImage = $objectManager->create('FishPig\WordPress\Helper\ResizeImage');
        return $reizeImage->getThumbnailImage($filename, 208, 151, true);
    }
}