Function allows a different notation for conditions.
Resembles the TYPOscript if.

#-- Example
[PIDinRootline = 10,20]
    TCEFORM.tt_content {
        header_layout {
            addItems {
                10 = MyNewHeader
            }
        }
    }
[global]
[userFunc = myfunc(someparams)]
    TCEFORM.tt_content {
        header_layout {
            disabled = 1
        }
    }
[global]

#-- can be rewritten as

TCEFORM.tt_content {
    header_layout {
        addItems {
            10 = MyNewHeader
            10.if.PIDinRootline = 10,20
        }
        disabled = 1
        disabled.if.userFunc = myfunc(someparams)
    }
}

#-- Differtent conditions are calculated only once per request
#-- Notice conditions are merged and they will overwrite each other

TCEFORM.tt_content {
    layout {
        addItems {
            10 = MyNewHeader
            if.userFunc = myFunc1()
        }
    }
}
TCEFORM.tt_content {
    layout {
        addItems {
            20 = MySecondHeader
            if.userFunc = myFunc2()
        }
    }
}
#-- above examples were intended to add
#--     10 when myFunc1 results in true and
#--     20 when myFunc2 results in true

#-- However this in invalid. After merging the result will be:
TCEFORM.tt_content {
    layout {
        addItems {
            10 = MyNewHeader
            20 = MySecondHeader
            if.userFunc = myFunc2()
        }
    }
}

#-- meaning items 10 and 20 are added when myFunc2 is true and myFunc1 is ignored.