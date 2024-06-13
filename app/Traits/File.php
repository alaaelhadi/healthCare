<?php 

namespace App\Traits;


Trait File{


    public static function uploadImage($request, string $path):string
    
    {
           $image=$request->file('orgnization_logo');
           $imageName=time() . $image->getClientOriginalName();
           $image->move($path, $imageName);
           return $imageName;
    }


    public static function DeleteImage(string $path):bool{

        $oldFilePath=public_path($path);
    
            if(file_exists($oldFilePath)){
                unlink($oldFilePath);
                return true;
              }
              return false;
        }
}
