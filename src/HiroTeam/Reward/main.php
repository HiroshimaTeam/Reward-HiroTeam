<?php
#██╗░░██╗██╗██████╗░░█████╗░████████╗███████╗░█████╗░███╗░░░███╗
#██║░░██║██║██╔══██╗██╔══██╗╚══██╔══╝██╔════╝██╔══██╗████╗░████║
#███████║██║██████╔╝██║░░██║░░░██║░░░█████╗░░███████║██╔████╔██║
#██╔══██║██║██╔══██╗██║░░██║░░░██║░░░██╔══╝░░██╔══██║██║╚██╔╝██║
#██║░░██║██║██║░░██║╚█████╔╝░░░██║░░░███████╗██║░░██║██║░╚═╝░██║
#╚═╝░░╚═╝╚═╝╚═╝░░╚═╝░╚════╝░░░░╚═╝░░░╚══════╝╚═╝░░╚═╝╚═╝░░░░░╚═╝
namespace HiroTeam\Reward;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class main extends PluginBase{

    /**
     * @var Config
     */
   public $db;

   public function onEnable()
   {
       $this->db = new Config($this->getDataFolder() . 'db.yml', CONFIG::YAML); //Base de donnée !
   }

   public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
   {
       $commandName = strtolower($command->getName());
       if($sender instanceof Player){

           switch ($commandName){
               case 'reward':
                   $playerName = $sender->getName();
                   $time = $this->db->get($playerName);
                   $timeNow = time();
                   if(empty($time)){
                       $time = 0;
                   }
                   if($sender->hasPermission('ninja.use'))
                   if($timeNow - $time >= (24 * 60 * 60)) { //Si time de maintenant - time de la derniere fois est p^lus que 24H
                       $sender->getInventory()->addItem(Item::get(1, 0, 1), Item::get(2, 0, 1)); //Donne les récompenses aux joueurs
                       $this->db->set($playerName, $timeNow);
                       $this->db->save();
                       $sender->sendMessage('§aVous avez bien récupéré votre récompense journalière !');
                   } else {
                       $HourMinuteSecond = explode(":", gmdate("H:i:s", (24 * 60 * 60) - ($timeNow - $time))); // 23:45:35
                       $sender->sendMessage("§cIl faut encore attendre $HourMinuteSecond[0] heure/s, $HourMinuteSecond[1] minute/s, $HourMinuteSecond[2] seconde/s avant de pouvoir récupérer ta prochaine récompense !");
                   }
                   break;
           }
       }
       return true;
   }
}