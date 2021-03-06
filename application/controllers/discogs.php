<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include './discogs-api/vendor/autoload.php';


class Discogs extends CI_Controller {


	public function index()
	{



	}

	public function fix()
	{

		/*
		if (!$this->ion_auth->is_admin())
		{
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}
		*/
		$service = new \Discogs\Service();
	


		$this->load->database();
		$this->db->select('*, release.id as release_id');
		$this->db->from('release');
		$this->db->join('artist', 'artist.id = release.artist_id');
		$this->db->join('music_genres', 'release.music_genres_id = music_genres.id');
		$this->db->join('labels', 'release.labels_id = labels.id','left');
		$this->db->where("release.discogsUrl LIKE  '' ");
		$this->db->limit(8);
		$this->db->offset(0);
		$this->db->order_by("release.views", "desc");
		$query = $this->db->get();
		$releases = $query->result();

		foreach ($releases as $r) {

			$qu = $r->artist_name.' - '.$r->title;

			echo "id: ".$r->release_id." - views:".$r->views."<br>";
			echo "QUERY: ".$qu."<br><br>";


			$resultset = $service->search(array(
				'q'     => $qu,
				));

			// Total results
			$total = count($resultset); 
			echo $total." results...<br>";

			if($total>0){
					// Total pages
				$pagination = $resultset->getPagination();

				$found = false;
					//echo count($pagination)."\n";

					// Fetch all results (use on your own risk, only one request per second allowed)
					//do {
				$pagination = $resultset->getPagination();
				echo "page: ".$pagination->getPage().'<br /><br />';
				$num = 1;
				foreach ($resultset as $result) {
					if($num<2){

						echo "#".$num." ".$result->getTitle().'<br />';
						echo "uri: http://www.discogs.com".$result->getUri().'<br>';
						echo "year: ".$result->getYear().'<br>';
						echo "updating ".$r->slug."...<br>";
						$this->load->database();
						$data = array(
							'discogsUrl'=> "http://www.discogs.com".$result->getUri(),
							'date' => $result->getYear()
							);

						$this->db->where('id',$r->release_id);
						$this->db->update('release', $data);


							//echo "NOTES: ".$result->getNotes();

						foreach ($result->getLabel() as $l) {
							echo "Label: ".$l.'<br>';
						}      
						foreach ($result->getGenre() as $g) {
							echo "genre: ".$g.'<br />';
						}

						$this->db->delete('release_music_style', array('release_id' => $r->release_id));

						foreach ($result->getStyle() as $s) {
							echo "  -".$s.'<br />';
							$query2 = $this->db->get_where('music_styles', array('style' => $s))->row();

							if (isset($query2->id))
							{
								echo "found";
								$styleId = $query2->id;
								        //error_log($discographyID);
							}
							else
							{
								echo "not found";

								$styleData = array(
									'style'=>$s
									);

								$this->db->insert('music_styles',$styleData);
								$styleId  =  $this->db->insert_id();

							}	
							$data = array(
								'release_id'=> $r->release_id,
								'music_style_id'=> $styleId,
								);

							$this->db->insert('release_music_style',$data);
							echo "<br><br>";
						} 
						echo "</br></br>";
						$num++;
						$found = true;
					}
				}
					//}while($resultset = $service->next($resultset) && (!$found));


			}else{
					
					$data = array(
							'discogsUrl'=> "not found",
							);

						$this->db->where('id',$r->release_id);
						$this->db->update('release', $data);

				
			}
			sleep(2);

		}


	}


	public function fixByID($id)
	{

		
		if (!$id)
		{
			//redirect them to the login page
			redirect('/', 'refresh');
		}
		
		$service = new \Discogs\Service();
	


		$this->load->database();
		$this->db->select('*, release.id as release_id');
		$this->db->from('release');
		$this->db->join('artist', 'artist.id = release.artist_id');
		$this->db->join('music_genres', 'release.music_genres_id = music_genres.id');
		$this->db->join('labels', 'release.labels_id = labels.id','left');
		$this->db->where("release.id",$id);
		$this->db->offset(0);
		$this->db->order_by("release.views", "desc");
		$query = $this->db->get();
		$releases = $query->result();

		foreach ($releases as $r) {

			$qu = $r->artist_name.' - '.$r->title;

			echo "id: ".$r->release_id." - views:".$r->views."<br>";
			echo "QUERY: ".$qu."<br><br>";


			$resultset = $service->search(array(
				'q'     => $qu,
				));

			// Total results
			$total = count($resultset); 
			echo $total." results...<br>";

			if($total>0){
					// Total pages
				$pagination = $resultset->getPagination();

				$found = false;
					//echo count($pagination)."\n";

					// Fetch all results (use on your own risk, only one request per second allowed)
					//do {
				$pagination = $resultset->getPagination();
				echo "page: ".$pagination->getPage().'<br /><br />';
				$num = 1;
				foreach ($resultset as $result) {
					if($num<2){

						echo "#".$num." ".$result->getTitle().'<br />';
						echo "uri: http://www.discogs.com".$result->getUri().'<br>';
						echo "year: ".$result->getYear().'<br>';
						echo "updating ".$r->slug."...<br>";
						$this->load->database();
						$data = array(
							'discogsUrl'=> "http://www.discogs.com".$result->getUri(),
							'date' => $result->getYear()
							);

						$this->db->where('id',$r->release_id);
						$this->db->update('release', $data);


							//echo "NOTES: ".$result->getNotes();

						foreach ($result->getLabel() as $l) {
							echo "Label: ".$l.'<br>';
						}      
						foreach ($result->getGenre() as $g) {
							echo "genre: ".$g.'<br />';
						}

						$this->db->delete('release_music_style', array('release_id' => $r->release_id));

						foreach ($result->getStyle() as $s) {
							echo "  -".$s.'<br />';
							$query2 = $this->db->get_where('music_styles', array('style' => $s))->row();

							if (isset($query2->id))
							{
								echo "found";
								$styleId = $query2->id;
								        //error_log($discographyID);
							}
							else
							{
								echo "not found";

								$styleData = array(
									'style'=>$s
									);

								$this->db->insert('music_styles',$styleData);
								$styleId  =  $this->db->insert_id();

							}	
							$data = array(
								'release_id'=> $r->release_id,
								'music_style_id'=> $styleId,
								);

							$this->db->insert('release_music_style',$data);
							echo "<br><br>";
						} 
						echo "</br></br>";
						$num++;
						$found = true;
					}
				}
					//}while($resultset = $service->next($resultset) && (!$found));


			}else{
					
					$data = array(
							'discogsUrl'=> "not found",
							);

						$this->db->where('id',$r->release_id);
						$this->db->update('release', $data);

				
			}
			sleep(2);

		}


	}


}
