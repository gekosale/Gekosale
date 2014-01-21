<?php

namespace Gekosale;
use PHPMailer;
use Exception;

include_once (ROOTPATH . 'lib' . DS . 'phpmailer' . DS . 'class.phpmailer.php');

class MailerModel extends Component\Model
{

    protected $viewid = NULL;

    protected $settings = Array(
        'mailer' => '',
        'fromname' => '',
        'fromemail' => '',
        'server' => '',
        'port' => '',
        'smtpsecure' => '',
        'smtpauth' => '',
        'smtpusername' => '',
        'smtppassword' => ''
    );

    protected $images = array();

    public function setViewId ($viewid)
    {
        if ($viewid == 0){
            $viewid = end(array_reverse(Helper::getViewIdsDefault()));
        }
        $this->viewid = $viewid;
    }

    public function saveSettings ($Data, $id)
    {
        $sql = 'INSERT INTO mailer SET
					viewid = :viewid,
					mailer = :mailer,
					fromname = :fromname,
					fromemail = :fromemail,
					server = :server,
					port = :port,
					smtpsecure = :smtpsecure,
					smtpauth = :smtpauth,
					smtpusername = :smtpusername,
					smtppassword = :smtppassword
				ON DUPLICATE KEY UPDATE
					mailer = :mailer,
					fromname = :fromname,
					fromemail = :fromemail,
					server = :server,
					port = :port,
					smtpsecure = :smtpsecure,
					smtpauth = :smtpauth,
					smtpusername = :smtpusername,
					smtppassword = :smtppassword';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('mailer', $Data['mailer']);
        $stmt->bindValue('fromname', $Data['fromname']);
        $stmt->bindValue('fromemail', $Data['fromemail']);
        $stmt->bindValue('server', $Data['server']);
        $stmt->bindValue('port', $Data['port']);
        $stmt->bindValue('smtpsecure', $Data['smtpsecure']);
        $stmt->bindValue('smtpauth', $Data['smtpauth']);
        $stmt->bindValue('smtpusername', $Data['smtpusername']);
        $stmt->bindValue('smtppassword', $Data['smtppassword']);
        $stmt->bindValue('viewid', $id);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new CoreException($this->trans('ERR_MAILER_DATA_SAVE'), 77, $e->getMessage());
        }
    }

    public function loadSettings ()
    {
        $sql = "SELECT
					M.mailer,
					M.fromname,
					M.fromemail,
					M.server,
					M.port,
					M.smtpsecure,
					M.smtpauth,
					M.smtpusername,
					M.smtppassword,
					V.pageschemeid,
					PS.templatefolder,
					V.photoid
				FROM mailer M
				LEFT JOIN view V ON V.idview = M.viewid
				LEFT JOIN pagescheme PS ON PS.idpagescheme = V.pageschemeid
				WHERE idview=:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $this->viewid);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            $this->settings = Array(
                'mailer' => $rs['mailer'],
                'fromname' => $rs['fromname'],
                'fromemail' => $rs['fromemail'],
                'server' => $rs['server'],
                'port' => $rs['port'],
                'smtpsecure' => $rs['smtpsecure'],
                'smtpauth' => $rs['smtpauth'],
                'smtpusername' => $rs['smtpusername'],
                'smtppassword' => $rs['smtppassword'],
                'theme' => $rs['templatefolder'],
                'photoid' => $rs['photoid']
            );
        }
    }

    public function getSettings ($viewid)
    {
        $this->setViewId($viewid);
        $this->loadSettings();
        return $this->settings;
    }

    public function loadContentToBody ($templateFile, $disable = false)
    {
        $path = $this->settings['theme'] . '/templates/email/';
        $headerTpl = $path . 'header.tpl';
        $contentTpl = $path . $templateFile . '.tpl';
        $footerTpl = $path . 'footer.tpl';
        $header = $this->registry->template->fetch($headerTpl);
        $content = $this->registry->template->fetch($contentTpl);
        $footer = $this->registry->template->fetch($footerTpl);
        return ($disable) ? $content : $header . $content . $footer;
    }

    public function setConfig ()
    {
        $Array = App::getConfig('phpmailer');
        try{
            $this->debugConfig($Array);
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
        switch ($Array['Mailer']) {
            case 'mail':
                $this->IsMail();
                $this->From = $Array['FromEmail'];
                break;
            case 'sendmail':
                $this->IsSendmail();
                $this->From = $Array['FromEmail'];
                break;
            case 'smtp':
                $this->IsSMTP();
                $this->Host = $Array['server'];
                $this->Port = $Array['port'];
                $this->SMTPSecure = $Array['SMTPSecure'];
                $this->SMTPAuth = $Array['SMTPAuth'];
                $this->From = $Array['FromEmail'];
                $this->Username = $Array['SMTPUsername'];
                $this->Password = $Array['SMTPPassword'];
                break;
            default:
                throw new Exception('Wrong e-mail sending method');
        }
        $this->CharSet = $Array['CharSet'];
        $this->FromName = $Array['FromName'];
        $this->IsHTML(true);
    }

    public function addEmbeddedImage ($path, $cid = '')
    {
        $this->images[] = array(
            'path' => $path,
            'cid' => empty($cid) ? basename($path) : $cid
        );
    }

    public function sendEmail ($Data)
    {
        $this->getSettings($Data['viewid']);
        
        $mailer = new PHPMailer(true);
        
        switch ($this->settings['mailer']) {
            case 'mail':
                $mailer->IsMail();
                $mailer->From = $this->settings['fromemail'];
                break;
            case 'sendmail':
                $mailer->IsSendmail();
                $mailer->From = $this->settings['fromemail'];
                break;
            case 'smtp':
                $mailer->IsSMTP();
                $mailer->Host = $this->settings['server'];
                $mailer->Port = $this->settings['port'];
                $mailer->SMTPSecure = $this->settings['smtpsecure'];
                $mailer->SMTPAuth = $this->settings['smtpauth'];
                $mailer->From = $this->settings['fromemail'];
                $mailer->Username = $this->settings['smtpusername'];
                $mailer->Password = $this->settings['smtppassword'];
                break;
        }
        $mailer->CharSet = 'UTF-8';
        $mailer->FromName = $this->settings['fromname'];
        $mailer->IsHTML(true);
        
        $disable = (isset($Data['disableLayout'])) ? $Data['disableLayout'] : false;
        $mailer->Body = $this->loadContentToBody($Data['template'], $disable);
        
        if ($this->images !== array()){
            foreach ($this->images as $image){
                $mailer->AddEmbeddedImage($image['path'], $image['cid']);
            }
        }
        
        $mailer->AddEmbeddedImage('./design/_images_frontend/core/logos/' . $this->settings['photoid'], 'logo', $this->settings['photoid']);
        
        if (isset($Data['bcc']) && $Data['bcc'] == TRUE){
            $mailer->addBCC($this->settings['fromemail']);
        }
        $mailer->Subject = $Data['subject'];
        
        foreach ($Data['email'] as $email){
            $mailer->addAddress($email);
            try{
                $mailer->Send();
                $mailer->ClearAddresses();
            }
            catch (phpmailerException $e){
                $exception = new \Gekosale\MailerException('Nieudana wysyłka maila: ' . $email, 0, $e->getMessage());
            }
            catch (Exception $e){
                $exception = new \Gekosale\MailerException('Nieudana wysyłka maila: ' . $email, 0, $e->getMessage());
            }
        }
    }
}
