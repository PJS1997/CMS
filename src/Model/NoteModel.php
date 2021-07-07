<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\StorageException;
use App\Exception\NotFoundException;
use PDO;
use Throwable;

class NoteModel extends AbstractModel implements ModelInterface
{
  public function list(
    int $pageNumber,
    int $pageSize,
    string $sortBy,
    string $sortOrder
  ): array {
    return $this->findBy(null, $pageNumber, $pageSize, $sortBy, $sortOrder);
  }

  public function search(
    string $phrase,
    int $pageNumber,
    int $pageSize,
    string $sortBy,
    string $sortOrder
  ): array {
    return $this->findBy($phrase, $pageNumber, $pageSize, $sortBy, $sortOrder);
  }

  public function count(): int
  {
    try {
      $query = "SELECT count(*) AS cn FROM cms";
      $result = $this->conn->query($query);
      $result = $result->fetch(PDO::FETCH_ASSOC);
      if ($result === false) {
        throw new StorageException('Błąd przy próbie pobrania ilości artykułów', 400);
      }

      return (int) $result['cn'];
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się pobrać informacji o liczbie artykułów', 400, $e);
    }
  }

  public function searchCount(string $phrase): int
  {
    try {
      $phrase = $this->conn->quote('%' . $phrase . '%', PDO::PARAM_STR);
      $query = "SELECT count(*) AS cn FROM cms WHERE title LIKE($phrase)";
      $result = $this->conn->query($query);
      $result = $result->fetch(PDO::FETCH_ASSOC);
      if ($result === false) {
        throw new StorageException('Błąd przy próbie pobrania ilości artykułów', 400);
      }

      return (int) $result['cn'];
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się pobrać informacji o liczbie artykułów', 400, $e);
    }
  }

  public function get(int $id): array
  {
    try {
      $query = "SELECT * FROM cms WHERE id = $id";
      $result = $this->conn->query($query);
      $note = $result->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się pobrać artykułu', 400, $e);
    }

    if (!$note) {
      throw new NotFoundException("Artykuł o id: $id nie istnieje");
    }

    return $note;
  }

  public function create(array $data): void
  {
    try {
      $title = $this->conn->quote($data['title']);
      $description = $this->conn->quote($data['description']);
      $status = $this->conn->quote($data['status']);
      $created = $this->conn->quote(date('Y-m-d H:i:s'));

      $query = "
        INSERT INTO cms(title, description, status, created)
        VALUES($title, $description, $status, $created)
      ";

      $this->conn->exec($query);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się utworzyć nowego artykułu', 400, $e);
    }
  }

  public function edit(int $id, array $data): void
  {
    try {
      $title = $this->conn->quote($data['title']);
      $description = $this->conn->quote($data['description']);
      $status = $this->conn->quote($data['status']);

      $query = "
        UPDATE cms
        SET title = $title, description = $description, status = $status
        WHERE id = $id
      ";

      $this->conn->exec($query);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się zaktualizować artykułu', 400, $e);
    }
  }

  public function delete(int $id): void
  {
    try {
      $query = "DELETE FROM cms WHERE id = $id LIMIT 1";
      $this->conn->exec($query);
      $query ="SET  @num := 0;
      UPDATE cms SET id = @num := (@num+1);
      ALTER TABLE cms AUTO_INCREMENT =1;";
      $this->conn->exec($query);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się usunąć artykułu', 400, $e);
    }
  }

  private function findBy(
    ?string $phrase,
    int $pageNumber,
    int $pageSize,
    string $sortBy,
    string $sortOrder
  ): array {
    try {
      $limit = $pageSize;
      $offset = ($pageNumber - 1) * $pageSize;

      if (!in_array($sortBy, ['created', 'title'])) {
        $sortBy = 'id';
      }

      if (!in_array($sortOrder, ['asc', 'desc'])) {
        $sortOrder = 'desc';
      }

      $wherePart = '';
      if ($phrase) {
        $phrase = $this->conn->quote('%' . $phrase . '%', PDO::PARAM_STR);
        $wherePart = "WHERE title LIKE ($phrase)";
      }

      $query = "
        SELECT id, title, status, created 
        FROM cms
        $wherePart
        ORDER BY $sortBy $sortOrder
        LIMIT $offset, $limit
      ";

      $result = $this->conn->query($query);
      return $result->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się pobrać artykułów', 400, $e);
    }
  }
}
