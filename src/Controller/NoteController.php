<?php

declare(strict_types=1);

namespace App\Controller;

class NoteController extends AbstractController
{
  private const PAGE_SIZE = 8;

  public function createAction(): void
  {
    if ($this->request->hasPost()) {
      $noteData = [
        'title' => $this->request->postParam('title'),
        'description' => $this->request->postParam('description'),
        'status' => $this->request->postParam('status')
      ];
      $this->noteModel->create($noteData);
      $this->redirect('/', ['before' => 'created']);
    }

    $this->view->render('create');
  }

  public function showAction(): void
  {
    $this->view->render(
      'show',
      ['note' => $this->getNote()]
    );
  }

  public function listAction(): void
  {
    $phrase = $this->request->getParam('phrase');
    $pageNumber = (int) $this->request->getParam('page', 1);
    $pageSize = (int) $this->request->getParam('pagesize', self::PAGE_SIZE);
    $sortBy = $this->request->getParam('sortby', 'id');
    $sortOrder = $this->request->getParam('sortorder', 'desc');

    if (!in_array($pageSize, [1, 5, 10, 25])) {
      $pageSize = self::PAGE_SIZE;
    }

    if ($phrase) {
      $noteList = $this->noteModel->search($phrase, $pageNumber, $pageSize, $sortBy, $sortOrder);
      $cms = $this->noteModel->searchCount($phrase);
    } else {
      $noteList = $this->noteModel->list($pageNumber, $pageSize, $sortBy, $sortOrder);
      $cms = $this->noteModel->count();
    }

    $this->view->render(
      'list',
      [
        'page' => [
          'number' => $pageNumber,
          'size' => $pageSize,
          'pages' => (int) ceil($cms / $pageSize)
        ],
        'phrase' => $phrase,
        'sort' => ['by' => $sortBy, 'order' => $sortOrder],
        'cms' => $noteList,
        'before' => $this->request->getParam('before'),
        'error' => $this->request->getParam('error')
      ]
    );
  }

  public function editAction(): void
  {

    if ($this->request->isPost()) {
      $noteId = (int) $this->request->postParam('id');
      $noteData = [
        'title' => $this->request->postParam('title'),
        'description' => $this->request->postParam('description'),
        'status' => $this->request->postParam('status')
      ];
      $this->noteModel->edit($noteId, $noteData);
      $this->redirect('/', ['before' => 'edited']);
    }

    $this->view->render(
      'edit',
      ['note' => $this->getNote()]
    );
  }

  public function deleteAction(): void
  {
    if ($this->request->isPost()) {
      $id = (int) $this->request->postParam('id');
      $this->noteModel->delete($id);
      $this->redirect('/', ['before' => 'deleted']);
    }

    $this->view->render(
      'delete',
      ['note' => $this->getNote()]
    );
  }

  private function getNote(): array
  {
    $noteId = (int) $this->request->getParam('id');
    if (!$noteId) {
      $this->redirect('/', ['error' => 'missingNoteId']);
    }

    return $this->noteModel->get($noteId);
  }
}
