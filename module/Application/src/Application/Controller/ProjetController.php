<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Projet;
use Application\Entity\Tache;
use Application\Entity\StatusProjet;

class ProjetController extends AbstractActionController {

    protected $_objectManager;

    public function listProjetAction()
    {
        $projets = $this->getObjectManager()->getRepository('\Application\Entity\Projet')->findAll();
         $sm = $this->getServiceLocator();
            $auth = $sm->get('zfcuser_auth_service');
            if ($auth->hasIdentity()) {
                $id_userco=$auth->getIdentity()->getId();                
            }

        foreach ($projets as $projet)
        {
           $status= $this->getObjectManager()->getRepository('\Application\Entity\Status')->findAll();
           $users = $this->getObjectManager()->getRepository('\ZfcUser\Entity\User')->findAll();
        }   
        if (!empty($status))
        {
              return new ViewModel(array('projets' => $projets, 'id_userco' => $id_userco,'users' => $users, 'status' => $status));                
        }     
          
    }

    public function listTacheAction()
    {
        $taches = $this->getObjectManager()->getRepository('\Application\Entity\Tache')->findAll();
        foreach ($taches as $tache)
        {
            $status= $this->getObjectManager()->getRepository('\Application\Entity\Status')->findAll();            
        }   
        return new ViewModel(array('taches' => $taches, 'status'=> $status));
    }

    public function voirProjetAction()
    {
        $id = (int) $this->params()->fromRoute('idprojet', 0);
        $projet = $this->getObjectManager()->find('\Application\Entity\Projet', $id);
        $taches = $this->getObjectManager()->getRepository('\Application\Entity\Tache')->findBy(array('id_projet' =>$id));
        $sm = $this->getServiceLocator();
            $auth = $sm->get('zfcuser_auth_service');
            if ($auth->hasIdentity()) {
                $id_userco=$auth->getIdentity()->getId();                
            }
        foreach ($taches as $tache)
        {
            $status= $this->getObjectManager()->getRepository('\Application\Entity\Status')->findAll();            
        }   
        
        return new ViewModel(array('projet' => $projet,'id_userco'=>$id_userco, 'taches' => $taches, 'status' => $status));
    }

    public function addProjetAction()
    {
        if ($this->request->isPost())
        {
            $projet = new Projet();
            $projet->setTitre_projet($this->getRequest()->getPost('titre_projet'));
            $projet->setDescription_projet($this->getRequest()->getPost('description_projet'));
            $projet->setDate_debut_projet(new \DateTime($this->getRequest()->getPost('date_debut_projet')));
            $projet->setDate_fin_projet(new \DateTime($this->getRequest()->getPost('date_fin_projet')));
            
            $sm = $this->getServiceLocator();
            $auth = $sm->get('zfcuser_auth_service');
            if ($auth->hasIdentity()) {
                $id_user=$auth->getIdentity()->getId();                
            }
            $projet->setId_status(1);
            $projet->setUser_id($id_user);
            
            $this->getObjectManager()->persist($projet);
            $this->getObjectManager()->flush();
            $newId = $projet->getId_projet();

            return $this->redirect()->toRoute('projet');
        }
        return new ViewModel();
    }

    public function AddTacheAction()
    {        
        if ($this->request->isPost())
        {
            $id_projet = (int) $this->params()->fromRoute('idprojet', 0);
            $tache = new Tache();
            $tache->setTitre_tache($this->getRequest()->getPost('titre_tache'));            
            $tache->setDescription_tache($this->getRequest()->getPost('description_tache'));
            $tache->setDate_debut(new \DateTime($this->getRequest()->getPost('date_debut')));
            $tache->setDate_fin(new \DateTime($this->getRequest()->getPost('date_fin')));            
            $tache->setId_projet($id_projet);
            $tache->setId_status(1);
            
            $this->getObjectManager()->persist($tache);
            $this->getObjectManager()->flush();
            $newId = $tache->getId_tache();
            return $this->redirect()->toRoute('projet', array('action' => 'voirProjet', 'idprojet' => $id_projet));
        }
    }

    public function editProjetAction()
    {
        $id = (int) $this->params()->fromRoute('idprojet', 0);
        $projet = $this->getObjectManager()->find('\Application\Entity\Projet', $id);
        $status= $this->getObjectManager()->getRepository('\Application\Entity\Status')->findAll();

        if ($this->request->isPost())
        {
            $projet->setTitre_projet($this->getRequest()->getPost('titre_projet'));
            $projet->setDescription_projet($this->getRequest()->getPost('description_projet'));
            $projet->setDate_debut_projet(new \DateTime($this->getRequest()->getPost('date_debut_projet')));
            $projet->setDate_fin_projet(new \DateTime($this->getRequest()->getPost('date_fin_projet')));
            $projet->setId_status($this->getRequest()->getPost('id_status'));

            $this->getObjectManager()->persist($projet);
            $this->getObjectManager()->flush();

            return $this->redirect()->toRoute('projet');
        }

        $viewModel=new ViewModel(array('projet' => $projet, 'status' => $status));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function editTacheAction()
    {
        $id = (int) $this->params()->fromRoute('idprojet', 0);
        $idtache = (int) $this->params()->fromRoute('idtache', 0);
        $projet = $this->getObjectManager()->find('\Application\Entity\Projet', $id);
        $tache = $this->getObjectManager()->find('\Application\Entity\Tache', $idtache);
        $status= $this->getObjectManager()->getRepository('\Application\Entity\Status')->findAll();
        if ($this->request->isPost())
        {
            $tache->setTitre_tache($this->getRequest()->getPost('titre_tache'));
            $tache->setDescription_tache($this->getRequest()->getPost('description_tache'));
            $tache->setDate_debut(new \DateTime($this->getRequest()->getPost('date_debut')));
            $tache->setDate_fin(new \DateTime($this->getRequest()->getPost('date_fin')));
            $tache->setId_status($this->getRequest()->getPost('id_status'));

            $this->getObjectManager()->persist($tache);
            $this->getObjectManager()->flush();

            return $this->redirect()->toRoute('projet', array('action' => 'voirProjet', 'idprojet' => $id));
        }
        $viewModel=new ViewModel(array('projet' => $projet, 'tache' => $tache, 'status' => $status));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function deleteProjetAction()
    {
        $id = (int) $this->params()->fromRoute('idprojet', 0);
        $projet = $this->getObjectManager()->find('\Application\Entity\Projet', $id);

        if ($this->request->isPost())
        {
            $this->getObjectManager()->remove($projet);
            $this->getObjectManager()->flush();

            return $this->redirect()->toRoute('projet');
        }

        $viewModel=new ViewModel(array('projet' => $projet));
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    public function deleteTacheAction()
    {
        $id = (int) $this->params()->fromRoute('idprojet', 0);
        $idtache = (int) $this->params()->fromRoute('idtache', 0);
        $projet = $this->getObjectManager()->find('\Application\Entity\Projet', $id);
        $tache = $this->getObjectManager()->find('\Application\Entity\Tache', $idtache);

        if ($this->request->isPost())
        {
            $this->getObjectManager()->remove($tache);
            $this->getObjectManager()->flush();

            return $this->redirect()->toRoute('projet', array('action' => 'voirProjet', 'idprojet' => $id));
        }

        $viewModel=new ViewModel(array('projet' => $projet, 'tache' => $tache));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function listuserAction()
    {
        $users = $this->getObjectManager()->getRepository('ZfcUser\Entity\User')->findAll();
        return new ViewModel(array('users'=>$users));
    }

    protected function getObjectManager()
    {
        if (!$this->_objectManager)
        {
            $this->_objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->_objectManager;
    }

}