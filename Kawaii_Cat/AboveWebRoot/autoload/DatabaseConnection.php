<?php
// database connection class that will be kept above web root
// can just use a static method here, no need to create an object

class DatabaseConnection {
	
	static function connect() {
		  return new DB\SQL(
			'mysql:host=localhost;port=3306;dbname=sedin112_Database',
			'sedin112_AnnaAloshine',
			'Edinburgh2016'
		  );
	}
	
}

?>
