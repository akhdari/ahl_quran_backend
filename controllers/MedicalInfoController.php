<?php

require_once '../../models/MedicalInfo.php';
require_once '../../controllers/Controller.php';

class MedicalInfoController extends Controller
{
    
    public static function getAll() {
        try {
            $obj = MedicalInfo::getAll(self::$dbconnection);
            if (!$obj) {
                self::sendResponse(404, ['error' => 'Not data']);
            } else {
                self::sendResponse(200, $obj);
            }
        } catch (\Exception $e) {
            self::sendResponse(500, ['error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public static function getOne($id) {
        try {
            $obj = MedicalInfo::get(self::$dbconnection, (int)$id);
            if (!$obj) {
                self::sendResponse(404, ['error' => 'Not found']);
            } else {
                self::sendResponse(200, $obj);
            }
        } catch (\Exception $e) {
            self::sendResponse(500, ['error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public static function save() {
        $data = self::getRequestBody();
        if (!$data) return;
        try {
            $obj = MedicalInfo::create(self::$dbconnection, $data);
            self::sendResponse(201, $obj);
        } catch (\Exception $e) {
            self::sendResponse(500, ['error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public static function edit($id) {
        self::getOne($id); // this already includes try-catch
    }

    public static function update($id) {
        $data = self::getRequestBody();
        if (!$data) return;

        try {
            $existing = MedicalInfo::get(self::$dbconnection, (int)$id);
            if (!$existing) {
                self::sendResponse(404, ['error' => 'Not found']);
                return;
            }
            foreach ($data as $k => $v) {
                if (property_exists($existing, $k)) {
                    $existing->$k = $v;
                }
            }
            if ($existing->update(self::$dbconnection)) {
                self::sendResponse(200, $existing);
            } else {
                self::sendResponse(500, ['error' => 'Update failed']);
            }
        } catch (\Exception $e) {
            self::sendResponse(500, ['error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public static function delete($id) {
        try {
            $existing = MedicalInfo::get(self::$dbconnection, (int)$id);
            if (!$existing) {
                self::sendResponse(404, ['error' => 'Not found']);
                return;
            }
            if ($existing->delete(self::$dbconnection)) {
                self::sendResponse(200, ['message' => 'Deleted']);
            } else {
                self::sendResponse(500, ['error' => 'Delete failed']);
            }
        } catch (\Exception $e) {
            self::sendResponse(500, ['error' => 'Server error: ' . $e->getMessage()]);
        }
    }
}
