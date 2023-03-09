<?php
    namespace App\Http\Traits;
    trait ResponseWithStatus{
        public function validationResponse($validator){
            return response()->json([
                'error'   => 'Validation Error',
                'message' => $validator->errors(),
            ]);
        }
        public function listResponse($message,$data = []){
            return response()->json([
                'success' => true,
                'message' => 'List All '.$message,
                'Data'    => $data,
            ]);
        }
        public function createResponse($message,$data = []){
            return response()->json([
                'success' => true,
                'message' => $message.' Created Successfully',
                'Data'    => $data,
            ]);
        }
        public function updateResponse($message,$data = []){
            return response()->json([
                'success' => true,
                'message' => $message.' Updated Successfully',
                'Data'    => $data,
            ]);
        }
        public function deleteResponse($message,$data = []){
            return response()->json([
                'success' => true,
                'message' => $message.' Deleted Successfully',
                'Data'    => $data,
            ]);
        }
        public function getResponse($message,$data = []){
            return response()->json([
                'success' => true,
                'message' => $message.' Get Successfully',
                'Data'    => $data,
            ]);
        }


    }
?>
