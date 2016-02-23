<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Hello World command for demo purposes.
 *
 * You could also extend from Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
 * to get access to the container via $this->getContainer().
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class ProviderCommand extends ContainerAwareCommand
{

    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {

        $this
            ->setName('app:provider')
            ->setDescription('The provider for a queue of tasks')
            ->addArgument('type', InputArgument::OPTIONAL, 'json/xml.', 'json')
            ->addArgument('directory', InputArgument::OPTIONAL, 'Files directory.', __DIR__ . '/tasks/source-json/')
            ->setHelp(<<<EOF
The <info>%command.name%</info> put tasks into the task queue.
You should put a task file in the tasks directory.
<info>php %command.full_name%</info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $directory = $input->getArgument('directory');
        $producer = $this->getContainer()->get('old_sound_rabbit_mq.upload_tasks_producer');
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if(is_file($directory . $file)){
                    $fileContent = file_get_contents($directory . $file);
                    if (mb_strtolower($type) == self::TYPE_JSON){
                        foreach ($this->getHotelsInfoFromJson(json_decode($fileContent)) as $item) {
                            if ($item->objectId){
                                $msg = array('id' => (string)$item->objectId, 'type' => $type, 'data' => json_encode($item));
                                $producer->publish(serialize($msg));
                            }
                        }
                    } elseif(mb_strtolower($type) == self::TYPE_XML){
                        $hotel = $this->getHotelsInfoFromXml($fileContent);
                        $msg = array('id' => (string)$hotel->Objectcode, 'type' => $type, 'data' => json_encode($hotel));
                        $producer->publish(serialize($msg));
                    } else {
                        throw new \Exception('Unknown file type!');
                    }
                }
            }
            closedir($handle);
        }
        $output->writeln(sprintf('DONE!'));
    }

    public function getHotelsInfoFromJson($data)
    {
        $array = (array)$data;
        $hotels = [];
        foreach ($array['hotels'] as $item) {
            $content = (array)$item->content->vendorContentList;
            $hotels[] = $content[0];
        }
        return $hotels;
    }


    public function getHotelsInfoFromXml($data)
    {
        $xmlObject = simplexml_load_string($data);
        return $xmlObject->data;
    }
}