<?php		//

	$dsn = 'mysql:dbname=データベース名;host=ホスト名';	//接続
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn,$user,$password);

	if(($_POST['name']!="")&&($_POST['comment']!="")&&($_POST['pass']!="")){	//投稿or編集
		if($_POST['hide']==""){		//通常の投稿
			$stmt = $pdo -> query("select max(id) as id_max from list");	//IDの最大値を取得
			$result = $stmt -> fetch(PDO::FETCH_ASSOC);
			$max = $result['id_max'];
			if(!$max){		//最初の投稿
				$id = 1;
			}else{			//追加の投稿
				$id = $max + 1;
			}
			//投稿の追加
			$stmt = $pdo -> prepare("INSERT INTO list (id,name,comment,time,password) VALUES (:id,:name,:comment,:time,:password)");
			$stmt -> bindValue(':id',$id,PDO::PARAM_INT);
			$stmt -> bindParam(':name',$name,PDO::PARAM_STR);
			$stmt -> bindParam(':comment',$comment,PDO::PARAM_STR);
			$stmt -> bindParam(':time',$time,PDO::PARAM_STR);
			$stmt -> bindParam(':password',$password,PDO::PARAM_STR);
			$name = htmlspecialchars($_POST['name']);			//名前
			$comment = htmlspecialchars($_POST['comment']);			//コメント
			$time = date("Y/m/d H:i:s");					//投稿時刻
			$password = htmlspecialchars($_POST['pass']);			//パスワード
			$stmt -> execute();

			$output = "新しく投稿しました。パスワードは忘れないでください！<br>";

		}else{			//編集

			$id = htmlspecialchars($_POST['hide']);
			$passin = htmlspecialchars($_POST['pass']);
			$stmt = $pdo -> query("select password as pass from list where id = $id");
			$result = $stmt -> fetch(PDO::FETCH_ASSOC);
			$password = $result['pass'];
			if($passin == $password){
				$stmt = $pdo -> prepare("update list set name =:name , comment = :comment where id =:id");
				$stmt -> bindParam(':name', $nm, PDO::PARAM_STR);
				$stmt -> bindParam(':comment', $kome, PDO::PARAM_STR);
				$stmt -> bindValue(':id', $id, PDO::PARAM_INT);
				$nm = htmlspecialchars($_POST['name']);
				$kome = htmlspecialchars($_POST['comment']);
				$stmt -> execute();
				$output = "ID: ".$id." の投稿内容を編集しました<br>";
			}else{
				$output = "パスワードが間違っています<br>";
			}
			$editnum = "";		//リセット
		}
	}else if(($_POST['sakujo']!="")&&($_POST['delpass']!="")){	//削除機能
		$id = htmlspecialchars($_POST['sakujo']);
		$passin = htmlspecialchars($_POST['delpass']);
		$stmt = $pdo -> query("select password as pass from list where id = $id");
		$result = $stmt -> fetch(PDO::FETCH_ASSOC);
		$password = $result['pass'];
		if($passin == $password){
			$stmt = $pdo -> prepare("DELETE FROM list where id =:id");
			$stmt -> bindValue(':id', $id, PDO::PARAM_INT);
			$stmt -> execute();
			$output = "ID: ".$id." の投稿内容を削除しました<br>";
		}else{
			$output = "パスワードが間違っています<br>";
		}
	}else if($_POST['editnum']!=""){//編集する投稿内容の取得
		$id = htmlspecialchars($_POST['editnum']);
		$stmt = $pdo -> prepare("SELECT * FROM list where id =:id");
		$stmt -> bindValue(':id', $id, PDO::PARAM_INT);
		$stmt -> execute();
		if($rows = $stmt -> fetch()){
			$editname = $rows["name"];
			$editcomment = $rows["comment"];
		}
		if(isset($editname)&&isset($editcomment)){
			$editnum = $id;
			$output = "ID: ".$id." の投稿内容を取得しました<br>";
		}
	}
?>
<html>
	<meta http-equiv="content-type" charset="utf-8">
	<form method="post" action="mission_4-1_matsui.php">
		名前　　　　：<input type="text" name="name" size="20" value="<?=$editname?>"><br>
		コメント　　：<input type="text" name="comment" size="40" value="<?=$editcomment?>"><br>
		パスワード　：<input type="text" name="pass" size="20">
		<input type="hidden" name="hide" value="<?=$editnum?>">
		<input type="submit" value="送信"><br><br>
		削除対象番号：<input type="text" name="sakujo" size="15"><br>
		パスワード　：<input type="text" name="delpass" size="20">
		<input type="submit" value="削除"><br><br>
		編集対象番号：<input type="text" name="editnum" size="15" value="">
		<input type="submit" value="編集">
	</form><br>
</html>
<?php			//コメント、リストの表示
	echo $output;
	echo "\n";
	echo "<hr>";
	$stmt = $pdo -> query('SELECT * FROM list ORDER by ID');
	while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
		echo "　".$row['id'].'　';
		echo $row['name'].'　';
		echo $row['comment'].'　';
		echo $row['time'].'<br>';
	}
	echo "<hr>";
?>