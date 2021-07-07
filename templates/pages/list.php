<div class="list">
  <section>
    <div class="message">
      <?php
      if (!empty($params['error'])) {
        switch ($params['error']) {
          case 'missingNoteId':
            echo 'Niepoprawny identyfikator artykułu';
            break;
          case 'noteNotFound':
            echo 'Artykuł nie został znaleziona';
            break;
        }
      }
      ?>
    </div>
    <div class="message">
      <?php
      if (!empty($params['before'])) {
        switch ($params['before']) {
          case 'created':
            echo 'Artykuł został utworzony';
            break;
          case 'deleted':
            echo 'Artykuł został usunięty';
            break;
          case 'edited':
            echo 'Artykuł został zaktualizowany';
            break;
        }
      }
      ?>
    </div>

    <?php

    $sort = $params['sort'] ?? [];
    $by = $sort['by'] ?? 'id';
    $order = $sort['order'] ?? 'desc';

    $page = $params['page'] ?? [];
    $size = $page['size'] ?? 8;
    $currentPage = $page['number'] ?? 1;
    $pages = $page['pages'] ?? 1;

    $phrase = $params['phrase'] ?? null;
    ?>

    

    <div class="tbl-header">
      <table cellpadding="0" cellspacing="0" border="0">
        <thead>
          <tr>
            <th>Id</th>
            <th>Tytuł</th>
            <th>Status</th>
            <th>Data</th>
            <th>Opcje</th>
          </tr>
        </thead>
      </table>
    </div>
    <div class="tbl-content">
      <table cellpadding="0" cellspacing="0" border="0">
        <tbody>
          <?php foreach ($params['cms'] ?? [] as $note) : ?>
            <tr>
              <td><?php echo $note['id'] ?></td>
              <td><?php echo $note['title'] ?></td>
              <td><?php echo $note['status'] ?></td>
              <td><?php echo $note['created'] ?></td>
              <td>
                <a href="/?action=show&id=<?php echo $note['id'] ?>">
                  <button>Szczegóły</button>
                </a>
                <a href="/?action=delete&id=<?php echo $note['id'] ?>">
                  <button>Usuń</button>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php
    $paginationUrl = "&phrase=$phrase&pagesize=$size?sortby=$by&sortorder=$order";
    ?>
    <ul class="pagination">
      <?php if ($currentPage !== 1) : ?>
        <li>
          <a href="/?page=<?php echo $currentPage - 1 . $paginationUrl ?>">
            <button> Poprzedni </button>
          </a>
        </li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $pages; $i++) : ?> <li>
          <a href="/?page=<?php echo $i . $paginationUrl ?>">
            <button><?php echo $i ?></button>
          </a>
        </li>
      <?php endfor; ?>
      <?php if ($currentPage < $pages) : ?>
        <li>
          <a href="/?page=<?php echo $currentPage + 1 . $paginationUrl ?>">
            <button> Następny </button>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </section>
</div>