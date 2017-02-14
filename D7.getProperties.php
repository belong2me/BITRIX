$dbElement = ElementTable::getList([
    'select' => ['ID', 'XML_ID', 'NAME', 'LINK', 'PROPERTY.ID', 'IBLOCK_ID'],
    'filter' => [
        '=IBLOCK_ID' => 3,
        '=LINK' => $elementID,
        '=PROPERTY.CODE' => 'CML2_LINK'
    ],
    'order' => ['ID'],
    'runtime' => [
        'PROPERTY' => [
            'data_type' => 'Bitrix\Iblock\PropertyTable',
            'reference' => ['=this.IBLOCK_ID' => 'ref.IBLOCK_ID'],
            'join_type' => "LEFT",
        ],
        'LINK' => [
            'data_type' => 'float',
            'expression' => [
                '(SELECT b_iblock_element_property.VALUE
                FROM b_iblock_element_property
                WHERE b_iblock_element_property.IBLOCK_PROPERTY_ID=%s
                    AND b_iblock_element_property.IBLOCK_ELEMENT_ID=%s)',
                'PROPERTY.ID',
                'ID',
            ],
        ],
    ],
]);