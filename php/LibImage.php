<?php
/**
 * Image处理
 */
class LibImage {
	
	/**
	 * 获取根目录
	 */
	private static function getRoot(){
		return realpath( dirname(__FILE__ ) . '/../' );
	}
	
	/**
	 * 获取图片信息
	 */
	public static function getImageInfo($path){
		$info = array();
		$imageSize = getimagesize($path);
		
		if($imageSize != false){
			$imageType = strtolower( substr(image_type_to_extension($imageSize[2]), 1) );
			$info = array(
				'width' => $imageSize[0],
				'height' => $imageSize[1],
				'type' => $imageType,
				'mime' => $imageSize['mime']
			);
		}
		
		return $info;
	}
	
	/**
	 * 裁剪图片
	 */
	public static function cropImage($path, $position, $newFolder=null, $quality=100, $delSource=false){
		$realPath = self::getRoot() . '/' . $path;
		$imageInfo = self::getImageInfo($realPath);
		
		if(empty( $imageInfo)) {
			return self::returnCMD(201, '图片不存在', array('path'=>$path));
		}
		
		switch($imageInfo['type']){
			case 'jpg':
			case 'jpeg':
				$image = imageCreateFromJpeg($realPath);
				break;
			case 'gif':
				$image = imageCreateFromGif($realPath);
				break;
			case 'png':
				$image = imageCreateFromPng($realPath);
				break;
			default:
				break;
		}
		
		$newImage = imageCreateTrueColor($position['w'], $position['h']);
		$background = imageColorAllocate($newImage, 255, 255, 255);
		imageFill($newImage, 0, 0, $background);
		
		imageCopyResampled(
			$newImage, $image, 0, 0, $position['x'], $position['y'],
			$position['w'], $position['h'], $position['x2'] - $position['x'], $position['y2'] - $position['y']
		);
		
		$newPath = ($newFolder ? $newFolder : dirname($path)) . '/' . date('YmdHis') . uniqid() . '.jpg';
		
		imageDestroy($image);
		imageJpeg($newImage, self::getRoot() . '/' . $newPath, $quality);
		
		if ($delSource) {
			unlink($realpath);
		}
		
		return self::returnCMD(200, '图片裁剪成功', array('path' => $newPath));
		
	}
	
	/**
	 * 返回数组
	 */
	public static function returnCMD($code=200, $msg='', $data=array()){
		if(!strlen( $msg )){
			$msg = $code == 200 ? '操作成功' : '操作失败';
		}
		
		$result = array(
			'code' => $code,
			'msg' => $msg,
			'data' => $data
		);
		
		return $result;
	}
}