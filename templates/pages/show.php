<div class="show">
  <?php $note = $params['note'] ?? null; ?>
  <?php if ($note) : ?>
    <ul>
      <li>Id: <?php echo $note['id'] ?></li>
      <li>Tytuł: <?php echo $note['title'] ?></li>
      <li>
        <pre>Opis:<br><?php echo $note['description'] ?></pre>
      </li>
      <li>
        Status: <?php echo $note['status'] ?>
      </li>
      <li>Zapisano: <?php echo $note['created'] ?></li>
    </ul>
    <a href="/?action=edit&id=<?php echo $note['id'] ?>">
      <button>Edytuj artykuł</button>
    </a>
  <?php else : ?>
    <div>Brak artykułu do wyświetlenia</div>
  <?php endif; ?>
  <a href="/">
    <button>Powrót do listy artykułów</button>
  </a>
</div>