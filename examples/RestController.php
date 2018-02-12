<?php

class RestController extends \yidas\rest\Controller
{
    /**
     * Action: Index
     * 
     * This method could be `public` property for none-routes usage
     */
    public function index() {}
        
    /**
     * Action: Store
     * 
     * @param array $requestData
     */
    protected function store($requestData=null) {}

    /**
     * Action: Show
     * 
     * @param int|string $resourceID
     */
    protected function show($resourceID) {}

    /**
     * Action: Update
     * 
     * @param int|string $resourceID
     * @param array $requestData
     */
    protected function update($resourceID, $requestData=null) {}

    /**
     * Action: Delete
     * 
     * @param int|string $resourceID
     */
    protected function delete($resourceID) {}    
}
