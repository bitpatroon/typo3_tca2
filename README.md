# typo3_tca2
Allows additional function like conditions to if in pageTSConfig

##USAGE

###Example 1

Conditionally add a new item 123 (myItem) on tt_content.layout.

    [userFunc = MyFunc(params)]
    TCEFORM.tt_content.layout {
        addItems {
            123 = myItem
        }
    }
    [global]

can be rewritten into 

    TCEFORM.tt_content.layout {
        addItems {
            123 = myItem
            123.userFunc = MyFunc(params)
        }
    }


###Example 2

Conditionally disable layout 1 on tt_content.

    [userFunc = MyFunc(params)]
    TCEFORM.tt_content.layout {
        disabled = 1
    }
    [global]

can be rewritten into 

    TCEFORM.tt_content.layout {
        disabled = 1
        disabled.userFunc = MyFunc(params)
    }

