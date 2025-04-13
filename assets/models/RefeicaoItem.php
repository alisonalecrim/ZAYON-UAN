class RefeicaoItem {
    private $conn;
    private $table_name = "refeicao_itens";

    public $id;
    public $refeicao_id;
    public $item_tipo;
    public $item_id;
    public $quantidade;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function adicionar() {
        $query = "INSERT INTO " . $this->table_name . " (refeicao_id, item_tipo, item_id, quantidade) VALUES (:refeicao_id, :item_tipo, :item_id, :quantidade)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':refeicao_id', $this->refeicao_id);
        $stmt->bindParam(':item_tipo', $this->item_tipo);
        $stmt->bindParam(':item_id', $this->item_id);
        $stmt->bindParam(':quantidade', $this->quantidade);

        return $stmt->execute();
    }
}
